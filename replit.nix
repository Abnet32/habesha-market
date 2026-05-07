{pkgs}: {
  deps = [
    pkgs.mariadb
    pkgs.php82Extensions.mysqli
    pkgs.unzip
  ];
}
