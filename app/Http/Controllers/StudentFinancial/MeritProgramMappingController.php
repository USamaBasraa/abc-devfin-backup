<?php

namespace App\Http\Controllers\StudentFinancial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Combined-programme mapping page. Calculation stays in MeritCalculationController.
 */
class MeritProgramMappingController extends MeritCalculationController
{
    /** @var array<int, array>|null */
    private $programRowsCache = null;

    public function index()
    {
        $meta    = $this->mappingMeta();
        $catalog = $this->programCatalog();

        return view('StudentFinancial.meritCombinedPrograms', [
            'groups'             => $this->mappingGroupsForView(),
            'catalog'            => $catalog,
            'schools'            => $this->mappingSchoolList($catalog),
            'excluded_school_id' => self::EXCLUDED_SCHOOL_ID,
            'count_from_label'   => 'Fall ' . self::MIN_SCHOLARSHIP_YEAR,
            'is_custom'          => $meta['custom'],
            'updated_at'         => $meta['updated_at'],
            'updated_by'         => $meta['updated_by'],
        ]);
    }

    public function save(Request $request)
    {
        $groups = $request->input('groups');

        if (!is_array($groups)) {
            $decoded = json_decode($request->getContent(), true);
            $groups  = is_array($decoded) && isset($decoded['groups'])
                ? $decoded['groups']
                : null;
        }

        if (!is_array($groups)) {
            return response()->json([
                'success' => false,
                'message' => 'Submit a list of combined programme groups.',
            ], 422);
        }

        $user      = auth()->user();
        $entryUser = $user && $user->id ? (string) $user->id : null;
        $result    = $this->saveStoredMapping($groups, $entryUser);

        if (!$result['ok']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        $this->combinedProgramsCache = $result['groups'];

        $hasMapping = $result['groups'] !== [];

        return response()->json([
            'success'    => true,
            'message'    => $result['message'],
            'groups'     => $this->mappingGroupsForView(),
            'is_custom'  => $hasMapping,
            'updated_at' => $hasMapping ? date('Y-m-d H:i:s') : null,
            'updated_by' => $hasMapping ? $entryUser : null,
        ]);
    }

    public function restore(Request $request)
    {
        try {
            DB::delete('DELETE FROM ' . self::MAP_TABLE);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not clear the mapping table.',
            ], 500);
        }

        $this->combinedProgramsCache = [];

        return response()->json([
            'success'    => true,
            'message'    => 'Built-in mapping restored. Calculation will use the original list until you save again.',
            'groups'     => $this->mappingGroupsForView(),
            'is_custom'  => false,
            'updated_at' => null,
            'updated_by' => null,
        ]);
    }

    private function mappingGroupsForView()
    {
        $byId = [];
        foreach ($this->programCatalog() as $row) {
            $byId[$row['id']] = $row;
        }

        $groups = [];

        foreach ($this->combinedPrograms() as $key => $cfg) {
            $programs = [];

            foreach ($cfg['ids'] as $id) {
                $id = (int) $id;
                $programs[] = isset($byId[$id]) ? $byId[$id] : [
                    'id'            => $id,
                    'name'          => 'Programme #' . $id,
                    'school_id'     => 0,
                    'school_name'   => '',
                    'school_code'   => '',
                    'degree_level'  => '',
                    'status'        => '',
                    'student_count' => 0,
                ];
            }

            $studentCount = 0;
            foreach ($programs as $program) {
                $studentCount += (int) ($program['student_count'] ?? 0);
            }

            $groups[] = [
                'key'           => $key,
                'name'          => $cfg['name'],
                'ids'           => array_map('intval', $cfg['ids']),
                'programs'      => $programs,
                'student_count' => $studentCount,
            ];
        }

        return $groups;
    }

    private function programCatalog()
    {
        if ($this->programRowsCache !== null) {
            return $this->programRowsCache;
        }

        try {
            $rows = DB::select(
                'SELECT p.prog_id, p.prog_name, p.prog_degree_level, p.prog_status,'
                . ' p.prog_school_id, s.school_name, s.school_code'
                . ' FROM programs p'
                . ' LEFT JOIN schools s ON s.school_id = p.prog_school_id'
                . ' ORDER BY s.school_name, p.prog_name'
            );
        } catch (Throwable $e) {
            report($e);
            $this->programRowsCache = [];

            return $this->programRowsCache;
        }

        $counts  = $this->programStudentCounts();
        $catalog = [];

        foreach ($rows as $row) {
            $r = array_change_key_case((array) $row, CASE_LOWER);

            if (!isset($r['prog_id']) || $r['prog_id'] === null) {
                continue;
            }

            $id   = (int) $r['prog_id'];
            $name = trim((string) ($r['prog_name'] ?? ''));

            if ($id <= 0) {
                continue;
            }

            $catalog[] = [
                'id'            => $id,
                'name'          => $name !== '' ? $name : ('Programme #' . $id),
                'school_id'     => (int) ($r['prog_school_id'] ?? 0),
                'school_name'   => trim((string) ($r['school_name'] ?? '')),
                'school_code'   => trim((string) ($r['school_code'] ?? '')),
                'degree_level'  => trim((string) ($r['prog_degree_level'] ?? '')),
                'status'        => strtoupper(trim((string) ($r['prog_status'] ?? ''))),
                'student_count' => isset($counts[$id]) ? $counts[$id] : 0,
            ];
        }

        $this->programRowsCache = $catalog;

        return $this->programRowsCache;
    }

    private function programStudentCounts()
    {
        try {
            $rows = DB::select(
                'SELECT sai.sai_sdpo_prog_id AS prog_id, COUNT(*) AS student_count'
                . ' FROM students_apply_info sai'
                . ' WHERE sai.sai_student_status IN (\'Active\', \'Expected-Graduate\')'
                . ' AND sai.sai_sdpo_school_id <> ?'
                . ' AND sai.sai_sdpo_sess_id >= ('
                . '     SELECT NVL(MIN(sess_id), 999999) FROM academic_sessions'
                . '     WHERE UPPER(sess_name) LIKE ?'
                . ' )'
                . ' GROUP BY sai.sai_sdpo_prog_id',
                [
                    self::EXCLUDED_SCHOOL_ID,
                    self::SCHOLARSHIP_SESSION_PREFIX . '%' . self::MIN_SCHOLARSHIP_YEAR . '%',
                ]
            );
        } catch (Throwable $e) {
            report($e);

            return [];
        }

        $map = [];

        foreach ($rows as $row) {
            $r  = array_change_key_case((array) $row, CASE_LOWER);
            $id = (int) ($r['prog_id'] ?? 0);
            if ($id > 0) {
                $map[$id] = (int) ($r['student_count'] ?? 0);
            }
        }

        return $map;
    }

    private function mappingSchoolList(array $catalog)
    {
        $schools = [];

        foreach ($catalog as $row) {
            $id = (int) ($row['school_id'] ?? 0);
            if ($id <= 0 || $id === self::EXCLUDED_SCHOOL_ID) {
                continue;
            }
            if (($row['status'] ?? '') !== 'E') {
                continue;
            }
            if (isset($schools[$id])) {
                continue;
            }

            $name = trim((string) ($row['school_name'] ?? ''));
            $code = trim((string) ($row['school_code'] ?? ''));
            if ($name === '') {
                $name = 'School ' . $id;
            }
            if ($code !== '') {
                $name .= ' (' . $code . ')';
            }

            $schools[$id] = ['id' => $id, 'name' => $name];
        }

        $schools = array_values($schools);
        usort($schools, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $schools;
    }

    private function mappingMeta()
    {
        $empty = ['custom' => false, 'updated_at' => null, 'updated_by' => null];

        try {
            $rows = DB::select(
                'SELECT * FROM ('
                . ' SELECT entry_user, entry_datetime FROM ' . self::MAP_TABLE
                . ' ORDER BY entry_datetime DESC'
                . ') WHERE ROWNUM = 1'
            );
        } catch (Throwable $e) {
            report($e);

            return $empty;
        }

        if (empty($rows)) {
            return $empty;
        }

        $r  = array_change_key_case((array) $rows[0], CASE_LOWER);
        $at = $r['entry_datetime'] ?? null;

        if ($at instanceof \DateTimeInterface) {
            $at = $at->format('Y-m-d H:i:s');
        } elseif ($at !== null) {
            $at = (string) $at;
        }

        return [
            'custom'     => true,
            'updated_at' => $at,
            'updated_by' => isset($r['entry_user']) ? (string) $r['entry_user'] : null,
        ];
    }

    private function saveStoredMapping(array $groups, $entryUser)
    {
        $seen    = [];
        $payload = [];

        foreach (array_values($groups) as $i => $group) {
            if (!is_array($group)) {
                return ['ok' => false, 'message' => 'Group ' . ($i + 1) . ' is not valid.', 'groups' => []];
            }

            $name = trim((string) ($group['name'] ?? ''));
            $ids  = [];
            foreach ((array) ($group['ids'] ?? []) as $id) {
                $n = (int) $id;
                if ($n > 0 && !in_array($n, $ids, true)) {
                    $ids[] = $n;
                }
            }

            if ($name === '') {
                return ['ok' => false, 'message' => 'Every group needs a combined programme name.', 'groups' => []];
            }
            // A group of one is valid: that programme competes alone under this
            // name. Do not require two IDs -- remove from a pair must be allowed
            // to leave a single programme in the mapping.
            if (count($ids) < 1) {
                return ['ok' => false, 'message' => '"' . $name . '" must include at least one programme ID.', 'groups' => []];
            }

            foreach ($ids as $id) {
                if (isset($seen[$id])) {
                    return [
                        'ok'      => false,
                        'message' => 'Programme ID ' . $id . ' is already in "' . $seen[$id] . '".',
                        'groups'  => [],
                    ];
                }
                $seen[$id] = $name;
            }

            $key = trim((string) ($group['key'] ?? ''));
            if ($key === '') {
                $key = implode('_', $ids);
            }

            $payload[] = ['key' => $key, 'name' => $name, 'ids' => $ids];
        }

        // An empty payload is a legitimate save -- it clears the mapping, and
        // every programme is then ranked on its own under PROGRAMS.PROG_NAME.
        $user = $entryUser === null || $entryUser === ''
            ? null
            : substr((string) $entryUser, 0, 100);

        try {
            DB::beginTransaction();
            DB::delete('DELETE FROM ' . self::MAP_TABLE);

            foreach ($payload as $group) {
                foreach (array_values($group['ids']) as $order => $progId) {
                    DB::insert(
                        'INSERT INTO ' . self::MAP_TABLE
                        . ' (GRP_KEY, GRP_NAME, PROG_ID, SORT_ORDER, ENTRY_USER, ENTRY_DATETIME)'
                        . ' VALUES (?, ?, ?, ?, ?, SYSTIMESTAMP)',
                        [$group['key'], $group['name'], $progId, $order + 1, $user]
                    );
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            try {
                DB::rollBack();
            } catch (Throwable $rollback) {
                report($rollback);
            }
            report($e);

            return [
                'ok'      => false,
                'message' => 'Could not save the mapping: ' . $e->getMessage(),
                'groups'  => [],
            ];
        }

        $map = [];
        foreach ($payload as $group) {
            $map[$group['key']] = ['ids' => $group['ids'], 'name' => $group['name']];
        }

        return [
            'ok'      => true,
            'message' => 'Combined programme mapping saved to the database.',
            'groups'  => $map,
        ];
    }
}
