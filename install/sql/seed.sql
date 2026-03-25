INSERT INTO roles (id, name) VALUES
  (1, 'admin'),
  (2, 'maintainer'),
  (3, 'user')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO operating_systems (name, slug, sort_order) VALUES
  ('Linux', 'linux', 1),
  ('Windows', 'windows', 2),
  ('macOS', 'macos', 3),
  ('Other', 'other', 99)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO architectures (name, slug, sort_order) VALUES
  ('x86', 'x86', 1),
  ('x86_64', 'x86_64', 2),
  ('amd64', 'amd64', 3),
  ('arm64', 'arm64', 4),
  ('armv7', 'armv7', 5),
  ('noarch', 'noarch', 6),
  ('universal', 'universal', 7),
  ('Other', 'other', 99)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO package_types (name, slug, sort_order) VALUES
  ('rpm', 'rpm', 1), ('deb', 'deb', 2), ('exe', 'exe', 3), ('msi', 'msi', 4), ('zip', 'zip', 5),
  ('tar.gz', 'tar-gz', 6), ('AppImage', 'appimage', 7), ('pkg', 'pkg', 8), ('dmg', 'dmg', 9), ('Other', 'other', 99)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO release_channels (name, slug, sort_order) VALUES
  ('stable', 'stable', 1), ('testing', 'testing', 2), ('beta', 'beta', 3), ('nightly', 'nightly', 4), ('custom', 'custom', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO settings (setting_key, setting_value) VALUES
  ('site_name', 'Software Artifacts Hub'),
  ('allow_registration', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
