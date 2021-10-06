use project;

set character set 'utf8';
set names 'utf8';

insert into user (id, email, is_banned, is_test, password, token, fullname)
  values (1, 'admin@project.local', 0, 0, '$2y$10$UVhQVjhFbGVMdjhFT2krSef4VWz9yAI7beOD4BM5MX0snB/wMWHHS',
          'SkZRbysyNE1QbkJCY1VBU3NpaTc4MGpRQmJqWFJhcEdlZTlnOW54OEQ0QXBTVWgrR3Z3UG5NUE4vdE1OdTZyL3JPVT0', 'Admin');
insert into user (id, email, is_banned, is_test, password, token, fullname)
  values (2, 'tester1@project.local', 0, 0, '$2y$10$ODBHM0hrMnVYaUd0RnBkU.9QenetVYpmp.hVC4v4Bar7yQL0SKFoi',
          'eEZRNlFtYVczV2FFWGxxeC9rOFhtcTFLSUI1V05nZ0trZVBwOHpCbDR5c0o3ZW5MOG9YMDRGVU91MGhwQVFlSG9yYz0', 'Tester One');
