<?php

namespace App\Services\StoreProcurement;

use App\Support\Oracle\ResolvesSynonymObjects;
use Illuminate\Support\Facades\DB;
use Throwable;

class PinnacleVendorSyncService
{
    use ResolvesSynonymObjects;

    private const SYNC_MANAGED_FIELDS = [
        'vendor_name',
        'vendor_contact_person',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'vendor_master_id',
        'supplier',
        'ntn_cnic',
        'attn',
        'beneficiary_name',
        'bank_name',
        'bank_iban',
    ];

    public function syncChunk(array $records): array
    {
        $this->ensurePinnacleSupplierColumn();
        $this->ensureManualEditColumns();

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $vendorCategoryId = $this->defaultVendorCategoryId();

        foreach ($records as $record) {
            $mapped = $this->mapRecord($record, $vendorCategoryId);

            if ($mapped === null) {
                $skipped++;
                continue;
            }

            $existingVendor = $this->findVendorByPinnacleSupplierId($mapped['pinnacle_supplier_id']);
            $exists = $existingVendor !== null;

            $this->mergeVendor($mapped, $existingVendor);

            if ($exists) {
                $updated++;
            } else {
                $inserted++;
            }
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
            'processed' => $inserted + $updated + $skipped,
        ];
    }

    private function mapRecord(array $record, $vendorCategoryId): ?array
    {
        $supplierId = $this->stringValue($record['ID'] ?? null, 50);
        $title = $this->stringValue($record['Title'] ?? null, 150);

        if ($supplierId === null || $title === null) {
            return null;
        }

        return [
            'pinnacle_supplier_id' => $supplierId,
            'vendor_name' => $title,
            'vendor_contact_person' => $this->stringValue($record['ContactPerson'] ?? null, 100),
            'vendor_phone' => $this->stringValue($record['MobileNo'] ?? null, 20),
            'vendor_email' => $this->stringValue($record['Email'] ?? null, 100),
            'vendor_address' => $this->stringValue($record['SAddress'] ?? null, 200),
            'vendor_category_id' => $vendorCategoryId,
            'supplier' => $supplierId,
            'ntn_cnic' => $this->stringValue(($record['NTN'] ?? null) ?: ($record['CNIC'] ?? null), 50),
            'attn' => $this->stringValue($record['ContactPerson'] ?? null, 255),
            'beneficiary_name' => $this->stringValue($record['BeneficiaryName'] ?? null, 255),
            'bank_name' => $this->stringValue($record['Bank_Title'] ?? null, 255),
            'bank_iban' => $this->stringValue($record['IBAN_NO'] ?? null, 50),
        ];
    }

    private function mergeVendor(array $vendor, ?array $existingVendor = null): void
    {
        $vendor = $this->applyProtectedFields($vendor, $existingVendor);

        DB::statement("
            MERGE INTO SP_VENDORS target
            USING (
                SELECT
                    :pinnacle_supplier_id AS PINNACLE_SUPPLIER_ID,
                    :vendor_name AS VENDOR_NAME,
                    :vendor_contact_person AS VENDOR_CONTACT_PERSON,
                    :vendor_phone AS VENDOR_PHONE,
                    :vendor_email AS VENDOR_EMAIL,
                    :vendor_address AS VENDOR_ADDRESS,
                    :vendor_category_id AS VENDOR_CATEGORY_ID,
                    :supplier AS SUPPLIER,
                    :ntn_cnic AS NTN_CNIC,
                    :attn AS ATTN,
                    :beneficiary_name AS BENEFICIARY_NAME,
                    :bank_name AS BANK_NAME,
                    :bank_iban AS BANK_IBAN,
                    :is_edited AS IS_EDITED,
                    :edited_fields AS EDITED_FIELDS
                FROM DUAL
            ) source
            ON (target.PINNACLE_SUPPLIER_ID = source.PINNACLE_SUPPLIER_ID)
            WHEN MATCHED THEN UPDATE SET
                target.VENDOR_NAME = source.VENDOR_NAME,
                target.VENDOR_CONTACT_PERSON = source.VENDOR_CONTACT_PERSON,
                target.VENDOR_PHONE = source.VENDOR_PHONE,
                target.VENDOR_EMAIL = source.VENDOR_EMAIL,
                target.VENDOR_ADDRESS = source.VENDOR_ADDRESS,
                target.SUPPLIER = source.SUPPLIER,
                target.NTN_CNIC = source.NTN_CNIC,
                target.ATTN = source.ATTN,
                target.BENEFICIARY_NAME = source.BENEFICIARY_NAME,
                target.BANK_NAME = source.BANK_NAME,
                target.BANK_IBAN = source.BANK_IBAN
            WHEN NOT MATCHED THEN INSERT (
                PINNACLE_SUPPLIER_ID,
                VENDOR_NAME,
                VENDOR_CONTACT_PERSON,
                VENDOR_PHONE,
                VENDOR_EMAIL,
                VENDOR_ADDRESS,
                VENDOR_CATEGORY_ID,
                VENDOR_REGISTRATION_DATE,
                SUPPLIER,
                NTN_CNIC,
                ATTN,
                BENEFICIARY_NAME,
                BANK_NAME,
                BANK_IBAN,
                IS_EDITED,
                EDITED_FIELDS
            ) VALUES (
                source.PINNACLE_SUPPLIER_ID,
                source.VENDOR_NAME,
                source.VENDOR_CONTACT_PERSON,
                source.VENDOR_PHONE,
                source.VENDOR_EMAIL,
                source.VENDOR_ADDRESS,
                source.VENDOR_CATEGORY_ID,
                SYSDATE,
                source.SUPPLIER,
                source.NTN_CNIC,
                source.ATTN,
                source.BENEFICIARY_NAME,
                source.BANK_NAME,
                source.BANK_IBAN,
                source.IS_EDITED,
                source.EDITED_FIELDS
            )
        ", $vendor);
    }

    private function findVendorByPinnacleSupplierId(string $pinnacleSupplierId): ?array
    {
        $rows = DB::select("
            SELECT
                VENDOR_ID,
                VENDOR_NAME,
                VENDOR_CONTACT_PERSON,
                VENDOR_PHONE,
                VENDOR_EMAIL,
                VENDOR_ADDRESS,
                VENDOR_CATEGORY_ID,
                SUPPLIER,
                NTN_CNIC,
                ATTN,
                BENEFICIARY_NAME,
                BANK_NAME,
                BANK_IBAN,
                IS_EDITED,
                EDITED_FIELDS
            FROM SP_VENDORS
            WHERE PINNACLE_SUPPLIER_ID = :pinnacle_supplier_id
            FETCH FIRST 1 ROWS ONLY
        ", ['pinnacle_supplier_id' => $pinnacleSupplierId]);

        if (empty($rows)) {
            return null;
        }

        return array_change_key_case((array) $rows[0], CASE_LOWER);
    }

    private function defaultVendorCategoryId()
    {
        if ($this->columnIsNullable('SP_VENDORS', 'VENDOR_CATEGORY_ID')) {
            return null;
        }

        $rows = DB::select("
            SELECT CATEGORY_ID
            FROM SP_VENDOR_CATEGORY
            ORDER BY CATEGORY_ID
            FETCH FIRST 1 ROWS ONLY
        ");

        return $rows[0]->category_id ?? $rows[0]->CATEGORY_ID ?? null;
    }

    private function ensurePinnacleSupplierColumn(): void
    {
        // Live: facultyconn → synonyms → bnucms. Never ALTER/CREATE INDEX from app.
        if (!$this->columnExists('SP_VENDORS', 'PINNACLE_SUPPLIER_ID')) {
            $this->logMissingOracleObject('SP_VENDORS.PINNACLE_SUPPLIER_ID', 'PinnacleVendorSyncService');
        }
    }

    private function ensureManualEditColumns(): void
    {
        foreach (['IS_EDITED', 'EDITED_FIELDS'] as $column) {
            if (!$this->columnExists('SP_VENDORS', $column)) {
                $this->logMissingOracleObject('SP_VENDORS.' . $column, 'PinnacleVendorSyncService');
            }
        }

        try {
            if ($this->columnExists('SP_VENDORS', 'IS_EDITED')) {
                DB::statement("UPDATE SP_VENDORS SET IS_EDITED = 0 WHERE IS_EDITED IS NULL");
            }

            if ($this->columnExists('SP_VENDORS', 'EDITED_FIELDS')) {
                DB::statement("UPDATE SP_VENDORS SET EDITED_FIELDS = '[]' WHERE EDITED_FIELDS IS NULL");
            }
        } catch (Throwable $e) {
            // DML may fail if privileges are read-only; do not break sync bootstrap.
        }
    }

    private function stringValue($value, int $maxLength): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return substr($value, 0, $maxLength);
    }

    private function applyProtectedFields(array $vendor, ?array $existingVendor): array
    {
        if ($existingVendor === null) {
            $vendor['is_edited'] = 0;
            $vendor['edited_fields'] = json_encode([]);

            return $vendor;
        }

        $editedFields = $this->normalizeEditedFields($existingVendor['edited_fields'] ?? null);

        foreach ($editedFields as $field) {
            if (in_array($field, self::SYNC_MANAGED_FIELDS, true) && array_key_exists($field, $existingVendor)) {
                $vendor[$field] = $existingVendor[$field];
            }
        }

        $vendor['is_edited'] = (int) ($existingVendor['is_edited'] ?? 0);
        $vendor['edited_fields'] = json_encode($editedFields);

        return $vendor;
    }

    private function normalizeEditedFields($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, 'is_string'));
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, 'is_string'));
    }
}
