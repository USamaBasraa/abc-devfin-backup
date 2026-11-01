-- Minimum stock alert threshold per store budget item (handover / issued page)
ALTER TABLE SP_STORE_BUDGET_ITEMS ADD WARNING_QTY NUMBER;
COMMENT ON COLUMN SP_STORE_BUDGET_ITEMS.WARNING_QTY IS 'Show order-again alert when available stock is at or below this quantity';
COMMIT;
