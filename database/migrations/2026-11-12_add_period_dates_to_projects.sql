-- Migration: Add start_date and end_date to tb_budget_projects
ALTER TABLE `tb_budget_projects` 
ADD COLUMN `start_date` DATE NULL DEFAULT NULL COMMENT 'วันที่เริ่มต้นห้วงเวลาโครงการ' AFTER `budget_source`,
ADD COLUMN `end_date` DATE NULL DEFAULT NULL COMMENT 'วันที่สิ้นสุดห้วงเวลาโครงการ' AFTER `start_date`;

