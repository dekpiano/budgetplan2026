-- ============================================================
-- Migration: Add ref_borrow_id and ref_doc_no to tb_budget_ledger
-- Fix: Unknown column 'ref_borrow_id' in 'SET'
-- Date: 2026-09-08
-- ============================================================

ALTER TABLE tb_budget_ledger
  ADD COLUMN ref_borrow_id INT UNSIGNED NULL COMMENT 'อ้างอิงรายการยืมเงิน' AFTER sub_item_id,
  ADD COLUMN ref_doc_no VARCHAR(100) NULL COMMENT 'เลขที่เอกสารอ้างอิง' AFTER ref_borrow_id,
  ADD KEY idx_ledger_ref_borrow (ref_borrow_id),
  ADD CONSTRAINT fk_ledger_ref_borrow FOREIGN KEY (ref_borrow_id) REFERENCES tb_budget_ledger(id) ON DELETE SET NULL ON UPDATE CASCADE;
