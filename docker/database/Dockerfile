FROM mysql:8.0

LABEL author="nurmat"
LABEL description="MAI Service database image"
LABEL version="1.0"

COPY ./docker/database/database.sql /docker-entrypoint-initdb.d/

EXPOSE 3306