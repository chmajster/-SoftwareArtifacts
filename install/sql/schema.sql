CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_blocked TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS operating_systems (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS architectures (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS package_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS release_channels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS repositories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  visibility ENUM('public','private') NOT NULL DEFAULT 'private',
  os_id INT,
  architecture_id INT,
  package_type_id INT,
  release_channel_id INT,
  version VARCHAR(50) DEFAULT '1.0.0',
  listing_enabled TINYINT(1) NOT NULL DEFAULT 1,
  signing_enabled TINYINT(1) NOT NULL DEFAULT 0,
  checksum_required TINYINT(1) NOT NULL DEFAULT 1,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  api_token_required TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_repo_owner(owner_id),
  INDEX idx_repo_visibility(visibility),
  FOREIGN KEY (owner_id) REFERENCES users(id),
  FOREIGN KEY (os_id) REFERENCES operating_systems(id),
  FOREIGN KEY (architecture_id) REFERENCES architectures(id),
  FOREIGN KEY (package_type_id) REFERENCES package_types(id),
  FOREIGN KEY (release_channel_id) REFERENCES release_channels(id)
);

CREATE TABLE IF NOT EXISTS repository_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  repository_id INT NOT NULL,
  user_id INT NOT NULL,
  role ENUM('maintainer','viewer') NOT NULL DEFAULT 'viewer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_repo_user(repository_id, user_id),
  FOREIGN KEY (repository_id) REFERENCES repositories(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS artifacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  repository_id INT NOT NULL,
  uploader_id INT NOT NULL,
  name VARCHAR(180) NOT NULL,
  version VARCHAR(60) NOT NULL,
  description TEXT,
  os_id INT,
  architecture_id INT,
  package_type_id INT,
  changelog MEDIUMTEXT,
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  is_latest TINYINT(1) NOT NULL DEFAULT 0,
  is_hidden TINYINT(1) NOT NULL DEFAULT 0,
  is_deprecated TINYINT(1) NOT NULL DEFAULT 0,
  file_original_name VARCHAR(255) NOT NULL,
  file_stored_name VARCHAR(255) NOT NULL,
  file_size_bytes BIGINT NOT NULL,
  file_mime_type VARCHAR(120),
  checksum_sha256 CHAR(64) NOT NULL,
  download_count INT NOT NULL DEFAULT 0,
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_art_repo(repository_id),
  INDEX idx_art_status(status),
  INDEX idx_art_version(version),
  FOREIGN KEY (repository_id) REFERENCES repositories(id) ON DELETE CASCADE,
  FOREIGN KEY (uploader_id) REFERENCES users(id),
  FOREIGN KEY (os_id) REFERENCES operating_systems(id),
  FOREIGN KEY (architecture_id) REFERENCES architectures(id),
  FOREIGN KEY (package_type_id) REFERENCES package_types(id)
);

CREATE TABLE IF NOT EXISTS downloads (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  artifact_id INT NOT NULL,
  repository_id INT NOT NULL,
  user_id INT NULL,
  ip_address VARCHAR(64) NOT NULL,
  user_agent VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_download_artifact(artifact_id),
  FOREIGN KEY (artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE,
  FOREIGN KEY (repository_id) REFERENCES repositories(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS activity_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(80) NOT NULL,
  entity_id INT NULL,
  meta_json JSON NULL,
  ip_address VARCHAR(64),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_logs_user(user_id),
  INDEX idx_logs_entity(entity_type, entity_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS password_resets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS api_tokens (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  repository_id INT NULL,
  name VARCHAR(100) NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  last_used_at DATETIME NULL,
  expires_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (repository_id) REFERENCES repositories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
