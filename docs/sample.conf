server {
    listen       80;
    server_name  test.com

    root /base-path/htdocs;

    # rewrite all request to index.php except whitelist
    if ( $uri !~ ^/(res|static|crossDomain\.xml|robots\.txt|favicon\.ico) ) {
        rewrite ^ /index.php last;
    }

    include public.conf;
}

server {
    listen       80;
    server_name  admin.test.com

    root /base-path/cube-admin/htdocs;

    if ( $uri !~ ^/(res|static|crossDomain\.xml|robots\.txt|favicon\.ico) ) {
        rewrite ^ /index.php last;
    }

    include public.conf;
}

server {
    listen       80;
    server_name  s.test.com

    root /base-path/htdocs_res;
}
