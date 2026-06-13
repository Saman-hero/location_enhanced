-- ============================================================
--  AutoLocation Enhanced — Database Setup
--  Run once: mysql -u root < setup.sql
-- ============================================================
CREATE DATABASE IF NOT EXISTS `location_enhanced`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `location_enhanced`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── users ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `nom`        VARCHAR(100) NOT NULL,
  `prenom`     VARCHAR(100) NOT NULL,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','operateur') DEFAULT 'operateur',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── vehicles ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id`                    INT AUTO_INCREMENT PRIMARY KEY,
  `numero`                VARCHAR(50)  NOT NULL UNIQUE,
  `immatriculation`       VARCHAR(20)  DEFAULT NULL,
  `marque`                VARCHAR(100) DEFAULT NULL,
  `modele`                VARCHAR(100) DEFAULT NULL,
  `annee`                 INT          DEFAULT NULL,
  `couleur`               VARCHAR(50)  DEFAULT NULL,
  `nb_places`             INT          DEFAULT 5,
  `categorie`             ENUM('économique','berline','SUV','premium','utilitaire') DEFAULT 'berline',
  `kilometrage`           INT          DEFAULT 0,
  `statut`                ENUM('disponible','loué','maintenance','indisponible') DEFAULT 'disponible',
  `prix_jour`             DECIMAL(10,2) DEFAULT 0.00,
  `caution`               DECIMAL(10,2) DEFAULT 0.00,
  `carburant`             ENUM('essence','diesel','hybride','électrique') DEFAULT 'essence',
  `transmission`          ENUM('manuelle','automatique') DEFAULT 'manuelle',
  `description`           TEXT DEFAULT NULL,
  `image_url`             VARCHAR(500) DEFAULT NULL,
  `intervalle_vidange`    INT          DEFAULT 10000,
  `derniere_vidange_km`   INT          DEFAULT NULL,
  `date_derniere_vidange` DATE         DEFAULT NULL,
  `created_at`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── clients ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `clients` (
  `id`                INT AUTO_INCREMENT PRIMARY KEY,
  `nom`               VARCHAR(100) NOT NULL,
  `prenom`            VARCHAR(100) NOT NULL,
  `email`             VARCHAR(150) DEFAULT NULL,
  `telephone`         VARCHAR(20)  DEFAULT NULL,
  `adresse`           TEXT         DEFAULT NULL,
  `ville`             VARCHAR(100) DEFAULT NULL,
  `cin`               VARCHAR(30)  DEFAULT NULL UNIQUE,
  `permis_numero`     VARCHAR(50)  DEFAULT NULL,
  `permis_categorie`  VARCHAR(20)  DEFAULT 'B',
  `permis_expiration` DATE         DEFAULT NULL,
  `type_client`       ENUM('particulier','entreprise') DEFAULT 'particulier',
  `entreprise`        VARCHAR(150) DEFAULT NULL,
  `statut`            ENUM('actif','suspendu','liste_noire') DEFAULT 'actif',
  `notes`             TEXT         DEFAULT NULL,
  `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── reservations ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reservations` (
  `id`                   INT AUTO_INCREMENT PRIMARY KEY,
  `reference`            VARCHAR(30)   NOT NULL UNIQUE,
  `client_id`            INT           NOT NULL,
  `vehicle_id`           INT           NOT NULL,
  `statut`               ENUM('en attente','confirmée','en cours','terminée','annulée') DEFAULT 'en attente',
  `date_debut`           DATETIME      NOT NULL,
  `date_fin_prevue`      DATETIME      NOT NULL,
  `date_retour_effectif` DATETIME      DEFAULT NULL,
  `lieu_depart`          VARCHAR(150)  DEFAULT NULL,
  `lieu_retour`          VARCHAR(150)  DEFAULT NULL,
  `km_depart`            INT           DEFAULT NULL,
  `km_retour`            INT           DEFAULT NULL,
  `prix_jour`            DECIMAL(10,2) DEFAULT NULL,
  `nb_jours`             INT           DEFAULT NULL,
  `caution`              DECIMAL(10,2) DEFAULT 0.00,
  `montant_total`        DECIMAL(10,2) DEFAULT NULL,
  `frais_extra`          DECIMAL(10,2) DEFAULT 0.00,
  `commentaire`          TEXT          DEFAULT NULL,
  `created_by`           INT           DEFAULT NULL,
  `created_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`client_id`)  REFERENCES `clients`(`id`),
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── paiements ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `paiements` (
  `id`                    INT AUTO_INCREMENT PRIMARY KEY,
  `reservation_id`        INT           NOT NULL,
  `montant`               DECIMAL(10,2) NOT NULL,
  `type_paiement`         ENUM('espèces','carte bancaire','virement','chèque') DEFAULT 'espèces',
  `type`                  ENUM('acompte','solde','caution','remboursement','frais extra') DEFAULT 'solde',
  `reference_transaction` VARCHAR(100)  DEFAULT NULL,
  `date_paiement`         DATE          NOT NULL,
  `notes`                 TEXT          DEFAULT NULL,
  `created_at`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── maintenance ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `maintenance` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id`       INT           NOT NULL,
  `type_maintenance` VARCHAR(100)  DEFAULT NULL,
  `description`      TEXT          DEFAULT NULL,
  `date_prevue`      DATE          DEFAULT NULL,
  `date_realisee`    DATE          DEFAULT NULL,
  `kilometrage`      INT           DEFAULT NULL,
  `cout`             DECIMAL(10,2) DEFAULT NULL,
  `technicien`       VARCHAR(100)  DEFAULT NULL,
  `statut`           ENUM('planifiée','en cours','terminée','annulée') DEFAULT 'planifiée',
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── sinistres (incidents) ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `sinistres` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `reference`       VARCHAR(30)   NOT NULL UNIQUE,
  `reservation_id`  INT           DEFAULT NULL,
  `vehicle_id`      INT           NOT NULL,
  `client_id`       INT           DEFAULT NULL,
  `type`            ENUM('accident','dommage','vol','panne','autre') DEFAULT 'dommage',
  `description`     TEXT          DEFAULT NULL,
  `cout_reparation` DECIMAL(10,2) DEFAULT NULL,
  `prise_en_charge` ENUM('client','assurance','société') DEFAULT 'client',
  `date_sinistre`   DATE          DEFAULT NULL,
  `statut`          ENUM('ouvert','en cours','clôturé') DEFAULT 'ouvert',
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`)    REFERENCES `vehicles`(`id`),
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`client_id`)     REFERENCES `clients`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── audit_log ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          DEFAULT NULL,
  `username`   VARCHAR(50)  DEFAULT NULL,
  `action`     VARCHAR(100) NOT NULL,
  `module`     VARCHAR(50)  NOT NULL,
  `details`    TEXT         DEFAULT NULL,
  `ip_address` VARCHAR(45)  DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── app_settings ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `app_settings` (
  `key`   VARCHAR(50) PRIMARY KEY,
  `value` VARCHAR(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

INSERT INTO `app_settings` (`key`, `value`) VALUES ('visit_count', '0')
  ON DUPLICATE KEY UPDATE `key` = `key`;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Seed Data ──────────────────────────────────────────────

-- Admin user (password: admin123)
-- password = "password" for all demo users
INSERT INTO `users` (`nom`, `prenom`, `username`, `password`, `role`) VALUES
('Admin', 'Système', 'admin', '$2y$10$/J6UyK7WJ/msdJ1k3LGbIOLqc8il1SdOwk2A/AvTa5kJWXro0nvlW', 'admin'),
('Dupont', 'Marie', 'marie', '$2y$10$/J6UyK7WJ/msdJ1k3LGbIOLqc8il1SdOwk2A/AvTa5kJWXro0nvlW', 'operateur');

-- Vehicles
INSERT INTO `vehicles` (`numero`,`immatriculation`,`marque`,`modele`,`annee`,`couleur`,`categorie`,`kilometrage`,`statut`,`prix_jour`,`caution`,`carburant`,`transmission`,`nb_places`) VALUES
('VH-001','234-A-1','Dacia','Sandero',2022,'Blanc','économique',45200,'disponible',250.00,3000.00,'essence','manuelle',5),
('VH-002','567-B-2','Toyota','Corolla',2023,'Gris','berline',22100,'disponible',400.00,5000.00,'hybride','automatique',5),
('VH-003','890-C-3','Hyundai','Tucson',2023,'Noir','SUV',18500,'disponible',600.00,7000.00,'essence','automatique',5),
('VH-004','123-D-4','Mercedes','Classe E',2022,'Argent','premium',35000,'loué',900.00,10000.00,'diesel','automatique',5),
('VH-005','456-E-5','Renault','Master',2021,'Blanc','utilitaire',61000,'maintenance',500.00,6000.00,'diesel','manuelle',9),
('VH-006','789-F-6','BMW','Série 3',2023,'Bleu','premium',12000,'disponible',850.00,9000.00,'diesel','automatique',5),
('VH-007','012-G-7','Peugeot','3008',2022,'Rouge','SUV',28000,'disponible',550.00,6500.00,'essence','automatique',5);

-- Clients
INSERT INTO `clients` (`nom`,`prenom`,`email`,`telephone`,`adresse`,`ville`,`cin`,`permis_numero`,`permis_expiration`,`type_client`,`statut`) VALUES
('Alami','Karim','k.alami@email.ma','0661234567','12 Rue Hassan II','Casablanca','BE123456','P-00123','2028-06-15','particulier','actif'),
('Benali','Fatima','f.benali@email.ma','0622334455','5 Avenue Mohammed V','Rabat','JE456789','P-00456','2027-03-20','particulier','actif'),
('El Fassi','Omar','o.elfassi@email.ma','0677889900','Quartier Palmier','Marrakech','CD789012','P-00789','2026-11-10','particulier','actif'),
('TransMaroc','','contact@transm.ma','0537001122','Zone Industrielle','Casablanca','RC-55432','E-00789','2029-01-01','entreprise','actif'),
('Tahiri','Nadia','n.tahiri@email.ma','0655443322','18 Rue des Fleurs','Fès','AB345678','P-01234','2025-08-05','particulier','suspendu');

-- Reservations
INSERT INTO `reservations` (`reference`,`client_id`,`vehicle_id`,`statut`,`date_debut`,`date_fin_prevue`,`lieu_depart`,`lieu_retour`,`km_depart`,`prix_jour`,`nb_jours`,`caution`,`montant_total`,`created_by`) VALUES
('LOC-2026-001',1,4,'en cours','2026-06-10 09:00:00','2026-06-14 09:00:00','Agence Casablanca','Agence Casablanca',34900,900.00,4,10000.00,3600.00,1),
('LOC-2026-002',2,1,'terminée','2026-05-20 10:00:00','2026-05-23 10:00:00','Agence Rabat','Agence Rabat',44800,250.00,3,3000.00,750.00,1),
('LOC-2026-003',3,3,'confirmée','2026-06-15 08:00:00','2026-06-18 08:00:00','Agence Marrakech','Agence Marrakech',18400,600.00,3,7000.00,1800.00,2),
('LOC-2026-004',4,5,'en attente','2026-06-20 09:00:00','2026-06-25 09:00:00','Agence Casablanca','Agence Casablanca',60800,500.00,5,6000.00,2500.00,1);

-- Payments
INSERT INTO `paiements` (`reservation_id`,`montant`,`type_paiement`,`type`,`date_paiement`) VALUES
(1,10000.00,'espèces','caution','2026-06-10'),
(1,1800.00,'carte bancaire','acompte','2026-06-10'),
(2,3000.00,'espèces','caution','2026-05-20'),
(2,750.00,'espèces','solde','2026-05-23');

-- Maintenance
INSERT INTO `maintenance` (`vehicle_id`,`type_maintenance`,`description`,`date_prevue`,`kilometrage`,`cout`,`statut`) VALUES
(5,'Vidange + Filtres','Vidange huile moteur et remplacement filtres','2026-06-08',61000,450.00,'en cours'),
(1,'Révision générale','Contrôle freins, niveaux, pneumatiques','2026-06-20',46000,800.00,'planifiée'),
(4,'Contrôle technique','Passage contrôle technique annuel','2026-07-01',36000,150.00,'planifiée');

-- Incidents
INSERT INTO `sinistres` (`reference`,`reservation_id`,`vehicle_id`,`client_id`,`type`,`description`,`cout_reparation`,`prise_en_charge`,`date_sinistre`,`statut`) VALUES
('SIN-2026-001',1,4,1,'dommage','Rayure portière avant droite lors du stationnement',1200.00,'assurance','2026-06-11','ouvert'),
('SIN-2026-002',2,1,2,'panne','Crevaison pneu arrière gauche',350.00,'client','2026-05-22','clôturé');

-- Audit log
INSERT INTO `audit_log` (`user_id`,`username`,`action`,`module`,`details`,`ip_address`) VALUES
(1,'admin','Connexion','auth','Connexion réussie','127.0.0.1'),
(1,'admin','Création réservation','reservations','LOC-2026-001 créée','127.0.0.1'),
(1,'admin','Création client','clients','Alami Karim ajouté','127.0.0.1');
