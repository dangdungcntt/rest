# rest-framework

Rest Framework - PHP Framework for ReactPHP Library

### Run
```
php index.php
```

### Docker
```
docker build -t <image_name>:<image_tag> .
docker run -d \
           -p 8080:8080 \
           -e APP_PORT="0.0.0.0:8080" \
           <image_name>:<image_tag> 
```

### Supervisor
```
[program:app-name]
process_name=%(program_name)s_%(process_num)02d
command=php /paht/to/project/index.php
autostart=true
autorestart=true
```

