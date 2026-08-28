-- ============================================================
-- BudgetPlan 2026 - Production Clean Install
-- Clean BudgetPlan-owned schema. No sample business data.
-- Does NOT touch external personnel/user tables.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS tb_budget_ledger;
DROP TABLE IF EXISTS tb_budget_project_sub_items;
DROP TABLE IF EXISTS tb_budget_projects;
DROP TABLE IF EXISTS tb_budget_tactics;
DROP TABLE IF EXISTS tb_budget_strategies;
DROP TABLE IF EXISTS tb_admin_system_access;
DROP TABLE IF EXISTS tb_admin_systems;
DROP TABLE IF EXISTS tb_admin_rloes;

CREATE TABLE tb_admin_rloes (
 admin_rloes_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 admin_rloes_userid VARCHAR(50) NOT NULL,
 admin_rloes_status VARCHAR(50) NOT NULL DEFAULT 'Member',
 admin_rloes_nanetype VARCHAR(255) NULL,
 admin_rloes_level INT UNSIGNED NOT NULL DEFAULT 0,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (admin_rloes_id), KEY idx_rloes_userid (admin_rloes_userid), KEY idx_rloes_status (admin_rloes_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_budget_strategies (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 budget_year VARCHAR(10) NOT NULL,
 strategy_no VARCHAR(20) NOT NULL,
 strategy_name VARCHAR(255) NOT NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (id), KEY idx_strategy_year_no (budget_year,strategy_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_budget_tactics (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 strategy_id INT UNSIGNED NOT NULL,
 tactic_no VARCHAR(20) NOT NULL,
 tactic_name VARCHAR(255) NOT NULL,
 objective TEXT NULL,
 actions TEXT NULL,
 responsible_agency VARCHAR(255) NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (id), KEY idx_tactic_strategy (strategy_id),
 CONSTRAINT fk_tactic_strategy FOREIGN KEY (strategy_id) REFERENCES tb_budget_strategies(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_budget_projects (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 budget_year VARCHAR(10) NOT NULL,
 strategy_id INT UNSIGNED NULL,
 tactic_id INT UNSIGNED NULL,
 strategy_name VARCHAR(255) NULL,
 strategy_sub VARCHAR(255) NULL,
 project_code VARCHAR(50) NULL,
 project_name VARCHAR(255) NOT NULL,
 budget_source VARCHAR(255) NULL,
 allocated_budget DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 status ENUM('active','closed') NOT NULL DEFAULT 'active',
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (id), KEY idx_project_year (budget_year), KEY idx_project_strategy (strategy_id), KEY idx_project_tactic (tactic_id),
 CONSTRAINT fk_project_strategy FOREIGN KEY (strategy_id) REFERENCES tb_budget_strategies(id) ON DELETE SET NULL ON UPDATE CASCADE,
 CONSTRAINT fk_project_tactic FOREIGN KEY (tactic_id) REFERENCES tb_budget_tactics(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_budget_project_sub_items (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 project_id INT UNSIGNED NOT NULL,
 item_no VARCHAR(50) NULL,
 item_name VARCHAR(255) NOT NULL,
 allocated_budget DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (id), KEY idx_subitem_project (project_id),
 CONSTRAINT fk_subitem_project FOREIGN KEY (project_id) REFERENCES tb_budget_projects(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_budget_ledger (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 project_id INT UNSIGNED NOT NULL,
 sub_item_id INT UNSIGNED NULL,
 doc_date DATE NULL,
 dika_no VARCHAR(100) NULL,
 doc_no VARCHAR(100) NULL,
 description TEXT NULL,
 amount_budget_adjust DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 amount_borrow DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 amount_return_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 amount_return_receipt DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 amount_direct_withdraw DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 notes VARCHAR(255) NULL,
 created_by VARCHAR(50) NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (id), KEY idx_ledger_project (project_id), KEY idx_ledger_date (doc_date), KEY idx_ledger_subitem (sub_item_id),
 CONSTRAINT fk_ledger_project FOREIGN KEY (project_id) REFERENCES tb_budget_projects(id) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT fk_ledger_subitem FOREIGN KEY (sub_item_id) REFERENCES tb_budget_project_sub_items(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_admin_systems (
 admin_system_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 admin_system_code VARCHAR(100) NOT NULL,
 admin_system_name VARCHAR(255) NOT NULL,
 admin_system_description VARCHAR(500) NULL,
 admin_system_icon VARCHAR(100) NULL,
 admin_system_url VARCHAR(255) NULL,
 admin_system_level INT UNSIGNED NOT NULL DEFAULT 1,
 admin_system_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (admin_system_id), UNIQUE KEY uk_system_code (admin_system_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tb_admin_system_access (
 admin_access_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 admin_access_userid VARCHAR(50) NOT NULL,
 admin_access_system_code VARCHAR(100) NOT NULL,
 admin_access_view TINYINT(1) NOT NULL DEFAULT 1,
 admin_access_create TINYINT(1) NOT NULL DEFAULT 0,
 admin_access_update TINYINT(1) NOT NULL DEFAULT 0,
 admin_access_delete TINYINT(1) NOT NULL DEFAULT 0,
 admin_access_approve TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 PRIMARY KEY (admin_access_id), KEY idx_access_user (admin_access_userid), KEY idx_access_system (admin_access_system_code), UNIQUE KEY uk_access_user_system (admin_access_userid,admin_access_system_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tb_admin_systems (admin_system_code,admin_system_name,admin_system_description,admin_system_icon,admin_system_url,admin_system_level,admin_system_active,created_at,updated_at) VALUES
('budget_ledger','ทะเบียนคุมเงินงบประมาณ','ทะเบียนคุมเงินงบประมาณ','bx-book-content','Admin/BudgetLedger',1,1,NOW(),NOW()),
('registry_purchase','ทะเบียนคุมใบสั่งซื้อ','ทะเบียนคุมใบสั่งซื้อ','bx-cart-alt','Admin/Registry/Purchase',2,1,NOW(),NOW()),
('registry_hire','ทะเบียนคุมใบสั่งจ้าง','ทะเบียนคุมใบสั่งจ้าง','bx-spreadsheet','Admin/Registry/Hire',3,1,NOW(),NOW()),
('procurement','ขั้นตอนจัดซื้อ / จัดจ้าง','ขั้นตอนจัดซื้อและจัดจ้าง','bx-cart','User/Procurement/Process',4,1,NOW(),NOW()),
('money_receipt','ใบสำคัญรับเงินตอบแทน','ใบสำคัญรับเงินตอบแทน','bx-file','User/Procurement/MoneyReceipt',5,1,NOW(),NOW()),
('budget_structure','ตั้งค่ายุทธศาสตร์และโครงการ','ตั้งค่ายุทธศาสตร์และโครงการ','bx-sitemap','Admin/BudgetLedger/Structure',6,1,NOW(),NOW());

SET FOREIGN_KEY_CHECKS=1;

-- No sample projects, strategies, tactics, sub-items, ledger transactions,
-- or user access rows are inserted.
