FROM alpine:latest
LABEL maintainer="dmpop@linux.com"
LABEL version="0.1"
LABEL description="Girasole container image"
RUN apk update
RUN apk add php-cli php-exif
COPY . /usr/src/girasole
WORKDIR /usr/src/girasole
EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:7000" ]
