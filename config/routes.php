<?php
/**
 * Route definitions
 * Format: [$router->get|post|add(method, page, ControllerClass, action)]
 *
 * Page param maps to: ?page=vehicles/add → VehicleController::create
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\VehicleController;
use App\Controllers\ReservationController;
use App\Controllers\ClientController;
use App\Controllers\PaymentController;
use App\Controllers\MaintenanceController;
use App\Controllers\IncidentController;
use App\Controllers\UserController;
use App\Controllers\AuditController;
use App\Controllers\ClientPortalController;

// ── Auth ─────────────────────────────────────────────────────
$router->get ('login',  AuthController::class, 'loginForm');
$router->post('login',  AuthController::class, 'login');
$router->get ('logout', AuthController::class, 'logout');

// ── Dashboard ────────────────────────────────────────────────
$router->get('',          DashboardController::class, 'index');
$router->get('dashboard', DashboardController::class, 'index');

// ── Vehicles ─────────────────────────────────────────────────
$router->get ('vehicles',         VehicleController::class, 'index');
$router->get ('vehicles/add',     VehicleController::class, 'create');
$router->post('vehicles/add',     VehicleController::class, 'create');
$router->get ('vehicles/edit',    VehicleController::class, 'edit');
$router->post('vehicles/edit',    VehicleController::class, 'edit');
$router->get ('vehicles/show',    VehicleController::class, 'show');
$router->get ('vehicles/delete',  VehicleController::class, 'delete');

// ── Reservations ─────────────────────────────────────────────
$router->get ('reservations',        ReservationController::class, 'index');
$router->get ('reservations/add',    ReservationController::class, 'create');
$router->post('reservations/add',    ReservationController::class, 'create');
$router->get ('reservations/edit',   ReservationController::class, 'edit');
$router->post('reservations/edit',   ReservationController::class, 'edit');
$router->get ('reservations/show',   ReservationController::class, 'show');
$router->get ('reservations/delete', ReservationController::class, 'delete');

// ── Clients ───────────────────────────────────────────────────
$router->get ('clients',        ClientController::class, 'index');
$router->get ('clients/add',    ClientController::class, 'create');
$router->post('clients/add',    ClientController::class, 'create');
$router->get ('clients/edit',   ClientController::class, 'edit');
$router->post('clients/edit',   ClientController::class, 'edit');
$router->get ('clients/show',   ClientController::class, 'show');
$router->get ('clients/delete', ClientController::class, 'delete');

// ── Payments ─────────────────────────────────────────────────
$router->get ('payments',     PaymentController::class, 'index');
$router->get ('payments/add', PaymentController::class, 'create');
$router->post('payments/add', PaymentController::class, 'create');

// ── Maintenance ───────────────────────────────────────────────
$router->get ('maintenance',        MaintenanceController::class, 'index');
$router->get ('maintenance/add',    MaintenanceController::class, 'create');
$router->post('maintenance/add',    MaintenanceController::class, 'create');
$router->get ('maintenance/edit',   MaintenanceController::class, 'edit');
$router->post('maintenance/edit',   MaintenanceController::class, 'edit');
$router->get ('maintenance/delete', MaintenanceController::class, 'delete');

// ── Incidents ─────────────────────────────────────────────────
$router->get ('incidents',        IncidentController::class, 'index');
$router->get ('incidents/add',    IncidentController::class, 'create');
$router->post('incidents/add',    IncidentController::class, 'create');
$router->get ('incidents/edit',   IncidentController::class, 'edit');
$router->post('incidents/edit',   IncidentController::class, 'edit');
$router->get ('incidents/delete', IncidentController::class, 'delete');

// ── Users ─────────────────────────────────────────────────────
$router->get ('users',        UserController::class, 'index');
$router->get ('users/add',    UserController::class, 'create');
$router->post('users/add',    UserController::class, 'create');
$router->get ('users/edit',   UserController::class, 'edit');
$router->post('users/edit',   UserController::class, 'edit');
$router->get ('users/delete', UserController::class, 'delete');

// ── Audit ─────────────────────────────────────────────────────
$router->get('audit', AuditController::class, 'index');

// ── Client Portal ─────────────────────────────────────────────
$router->get ('client',              ClientPortalController::class, 'index');
$router->get ('client/reserve',      ClientPortalController::class, 'reserve');
$router->post('client/reserve',      ClientPortalController::class, 'reserve');
$router->get ('client/confirmation', ClientPortalController::class, 'confirmation');
