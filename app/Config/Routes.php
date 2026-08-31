<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'ConUserHome::index');
$routes->get('User/BudgetSummary', 'ConUserHome::BudgetSummary');
$routes->get('User/Projects', 'ConUserHome::BudgetSummary');
$routes->get('User/getProjectLedger/(:num)', 'ConUserHome::getProjectLedger/$1');
$routes->get('User/Plan', 'ConUserPlan::index');
//User งานจองห้อง

$routes->match(['GET', 'POST'], '/LoginOfficerBudgetPlan', 'ConLogin::LoginOfficerPersonnel');
$routes->match(['GET', 'POST'], '/LoginOfficerPersonnel', 'ConLogin::LoginOfficerPersonnel');
$routes->match(['GET', 'POST'], 'Auth/googleLogin', 'ConLogin::LoginOfficerPersonnel');
$routes->match(['GET', 'POST'], '/Auth/googleLogin', 'ConLogin::LoginOfficerPersonnel');
$routes->get('/LogoutOfficerBudgetPlan', 'ConLogin::LogoutOfficerPersonnel');
$routes->get('/LogoutOfficerGeneral', 'ConLogin::LogoutOfficerPersonnel');
$routes->get('/LogoutOfficerPersonnel', 'ConLogin::LogoutOfficerPersonnel');
$routes->get('/Logout', 'ConLogin::LogoutOfficerPersonnel');

//จัดซื้อจัดจ้าง
$routes->get('User/Procurement/Process', 'ConUserProcurement::ProcurementProcess');
$routes->get('User/Registry/Purchase', 'ConUserProcurement::registry/purchase');
$routes->get('User/Registry/Hire', 'ConUserProcurement::registry/hire');
$routes->get('User/Registry/PrintOrder/(:num)', 'ConUserProcurement::printOrder/$1');
$routes->post('User/Registry/UploadImage/(:num)', 'ConUserProcurement::uploadImage/$1');
$routes->post('User/Registry/ReplaceImages/(:num)', 'ConUserProcurement::replaceImages/$1');

// Inspector (ผู้ตรวจรับ) Login — แยกจาก admin login
$routes->get('Inspector/Login', 'ConLogin::inspectorLoginUrl');

//ใบสำคัญรับเงินตอบแทนค่าวิทยากร
$routes->get('User/Procurement/MoneyReceipt', 'ConUserMoneyReceipt::MoneyReceiptForm');

// ทะเบียนคุมเงินงบประมาณรายจ่าย (Budget Ledger)
$routes->get('Admin/BudgetLedger', 'ConAdminBudgetLedger::index');
$routes->get('Admin/BudgetLedger/Structure', 'ConAdminBudgetLedger::structure');
$routes->post('Admin/BudgetLedger/saveStrategy', 'ConAdminBudgetLedger::saveStrategy');
$routes->post('Admin/BudgetLedger/deleteStrategy/(:num)', 'ConAdminBudgetLedger::deleteStrategy/$1');
$routes->post('Admin/BudgetLedger/saveTactic', 'ConAdminBudgetLedger::saveTactic');
$routes->post('Admin/BudgetLedger/deleteTactic/(:num)', 'ConAdminBudgetLedger::deleteTactic/$1');
$routes->get('Admin/BudgetLedger/getTacticsByStrategy/(:num)', 'ConAdminBudgetLedger::getTacticsByStrategy/$1');
$routes->post('Admin/BudgetLedger/saveProject', 'ConAdminBudgetLedger::saveProject');
$routes->post('Admin/BudgetLedger/deleteProject/(:num)', 'ConAdminBudgetLedger::deleteProject/$1');
$routes->get('Admin/BudgetLedger/view/(:num)', 'ConAdminBudgetLedger::viewLedger/$1');
$routes->get('Admin/BudgetLedger/getLedgerData/(:num)', 'ConAdminBudgetLedger::getLedgerData/$1');
$routes->get('Admin/BudgetLedger/getUnclearedBorrows/(:num)', 'ConAdminBudgetLedger::getUnclearedBorrows/$1');
$routes->post('Admin/BudgetLedger/saveLedgerItem', 'ConAdminBudgetLedger::saveLedgerItem');
$routes->post('Admin/BudgetLedger/deleteLedgerItem/(:num)', 'ConAdminBudgetLedger::deleteLedgerItem/$1');
$routes->get('Admin/BudgetLedger/print/(:num)', 'ConAdminBudgetLedger::printLedger/$1');

//Admin
$routes->get('Admin/Home', 'ConAdminHome::index');
$routes->get('Admin/Registry/Purchase', 'ConAdminHome::registry/purchase');
$routes->get('Admin/Registry/Hire', 'ConAdminHome::registry/hire');
$routes->post('Admin/Registry/SaveOrder', 'ConAdminHome::saveOrder');
$routes->post('Admin/Registry/DeleteOrder/(:num)', 'ConAdminHome::deleteOrder/$1');
$routes->get('Admin/Registry/PrintOrder/(:num)', 'ConAdminHome::printOrder/$1');
$routes->get('Admin/LocationRoom/LocationRoomMain', 'ConAdminLocationRoom::LocationRoomMain');

$routes->get('Admin/Rloes/Setting', 'ConAdminRoles::index');
$routes->post('Admin/Rloes/saveRoleAssignments', 'ConAdminRoles::saveRoleAssignments');
$routes->post('Admin/Rloes/savePersonnelRoles', 'ConAdminRoles::savePersonnelRoles');
$routes->post('Admin/Rloes/deletePersonnelRoles/(:segment)', 'ConAdminRoles::deletePersonnelRoles/$1');
$routes->match(['GET', 'POST'],'Admin/Rloes/RloesSettingManager', 'ConAdminRoles::RloesSettingManager');
$routes->post('Admin/Rloes/addRole', 'ConAdminRoles::addRole');
$routes->post('Admin/Rloes/updateRole/(:segment)', 'ConAdminRoles::updateRole/$1');
$routes->post('Admin/Rloes/deleteRole/(:segment)', 'ConAdminRoles::deleteRole/$1');

//Admin Person
$routes->get('Admin/WorkPerson/BudgetPlan', 'ConAdminWorkPerson::index');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

// ทะเบียนคุมงบประมาณข้อบัญญัติ
$routes->get('Admin/BudgetOrdinance', 'ConAdminBudgetOrdinance::index');

// 1. เบิกค่าน้ำมันเชื้อเพลิง
$routes->get('Admin/BudgetOrdinance/Oil', 'ConAdminBudgetOrdinance::oil');
$routes->get('Admin/BudgetOrdinance/Oil/getOilData/(:num)', 'ConAdminBudgetOrdinance::getOilData/$1');
$routes->get('Admin/BudgetOrdinance/Oil/print', 'ConAdminBudgetOrdinance::printOil');
$routes->post('Admin/BudgetOrdinance/Oil/Save', 'ConAdminBudgetOrdinance::saveOil');
$routes->post('Admin/BudgetOrdinance/Oil/Delete/(:num)', 'ConAdminBudgetOrdinance::deleteOil/$1');

// 2. ค่าใช้จ่ายเดินทางไปราชการ
$routes->get('Admin/BudgetOrdinance/Travel', 'ConAdminBudgetOrdinance::travel');
$routes->get('Admin/BudgetOrdinance/Travel/getTravelData/(:num)', 'ConAdminBudgetOrdinance::getTravelData/$1');
$routes->get('Admin/BudgetOrdinance/Travel/print', 'ConAdminBudgetOrdinance::printTravel');
$routes->post('Admin/BudgetOrdinance/Travel/Save', 'ConAdminBudgetOrdinance::saveTravel');
$routes->post('Admin/BudgetOrdinance/Travel/Delete/(:num)', 'ConAdminBudgetOrdinance::deleteTravel/$1');

// 3. ค่าลงทะเบียนอบรม / สัมมนา
$routes->get('Admin/BudgetOrdinance/Registration', 'ConAdminBudgetOrdinance::registration');
$routes->get('Admin/BudgetOrdinance/Registration/getRegistrationData/(:num)', 'ConAdminBudgetOrdinance::getRegistrationData/$1');
$routes->get('Admin/BudgetOrdinance/Registration/print', 'ConAdminBudgetOrdinance::printRegistration');
$routes->post('Admin/BudgetOrdinance/Registration/Save', 'ConAdminBudgetOrdinance::saveRegistration');
$routes->post('Admin/BudgetOrdinance/Registration/Delete/(:num)', 'ConAdminBudgetOrdinance::deleteRegistration/$1');

