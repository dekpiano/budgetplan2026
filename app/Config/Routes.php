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

//ใบสำคัญรับเงินตอบแทนค่าวิทยากร
$routes->get('User/Procurement/MoneyReceipt', 'ConUserMoneyReceipt::MoneyReceiptForm');

//Admin
$routes->get('Admin/Home', 'ConAdminHome::index');
$routes->get('Admin/LocationRoom/LocationRoomMain', 'ConAdminLocationRoom::LocationRoomMain');

$routes->get('Admin/Rloes/Setting', 'ConAdminRoles::index');
$routes->match(['GET', 'POST'],'Admin/Rloes/RloesSettingManager', 'ConAdminRoles::RloesSettingManager');

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
