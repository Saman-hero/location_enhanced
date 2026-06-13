-- ============================================================
--  AutoLocation Enhanced — Railway Setup
--  Paste this in Railway's MySQL query panel (no CREATE DATABASE / USE)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

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

-- ── sinistres ──────────────────────────────────────────────
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
  FOREIGN KEY (`vehicle_id`)     REFERENCES `vehicles`(`id`),
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`client_id`)      REFERENCES `clients`(`id`) ON DELETE SET NULL
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

SET FOREIGN_KEY_CHECKS = 1;

-- ── Seed Data ──────────────────────────────────────────────

-- Admin user  (login: admin / password: admin123)
INSERT IGNORE INTO `users` (`nom`,`prenom`,`username`,`password`,`role`) VALUES
('Admin','Système','admin','$2y$10$/J6UyK7WJ/msdJ1k3LGbIOLqc8il1SdOwk2A/AvTa5kJWXro0nvlW','admin'),
('Dupont','Marie','marie','$2y$10$/J6UyK7WJ/msdJ1k3LGbIOLqc8il1SdOwk2A/AvTa5kJWXro0nvlW','operateur');

-- Vehicles
INSERT IGNORE INTO `vehicles` (`numero`,`immatriculation`,`marque`,`modele`,`annee`,`couleur`,`categorie`,`kilometrage`,`statut`,`prix_jour`,`caution`,`carburant`,`transmission`,`nb_places`) VALUES
('VH-001','234-A-1','Dacia','Sandero',2022,'Blanc','économique',45200,'disponible',250.00,3000.00,'essence','manuelle',5),
('VH-002','567-B-2','Toyota','Corolla',2023,'Gris','berline',22100,'disponible',400.00,5000.00,'hybride','automatique',5),
('VH-003','890-C-3','Hyundai','Tucson',2023,'Noir','SUV',18500,'disponible',600.00,7000.00,'essence','automatique',5),
('VH-004','123-D-4','Mercedes','Classe E',2022,'Argent','premium',35000,'disponible',900.00,10000.00,'diesel','automatique',5),
('VH-005','456-E-5','Renault','Master',2021,'Blanc','utilitaire',61000,'disponible',500.00,6000.00,'diesel','manuelle',9),
('VH-006','789-F-6','BMW','Série 3',2023,'Bleu','premium',12000,'disponible',850.00,9000.00,'diesel','automatique',5),
('VH-007','012-G-7','Peugeot','3008',2022,'Rouge','SUV',28000,'disponible',550.00,6500.00,'essence','automatique',5);

-- Clients
INSERT IGNORE INTO `clients` (`nom`,`prenom`,`email`,`telephone`,`ville`,`cin`,`permis_numero`,`permis_expiration`,`type_client`,`statut`) VALUES
('Alami','Karim','k.alami@email.ma','0661234567','Casablanca','BE123456','P-00123','2028-06-15','particulier','actif'),
('Benali','Fatima','f.benali@email.ma','0622334455','Rabat','JE456789','P-00456','2027-03-20','particulier','actif'),
('El Fassi','Omar','o.elfassi@email.ma','0677889900','Marrakech','CD789012','P-00789','2026-11-10','particulier','actif');
