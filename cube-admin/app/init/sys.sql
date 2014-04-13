GRANT ALL PRIVILEGES ON *.* TO share@localhost;
create user share@localhost  IDENTIFIED  by 'share';
SET PASSWORD FOR share@localhost = PASSWORD('share123'); 
flush privileges;
