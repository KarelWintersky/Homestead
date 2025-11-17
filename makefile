#!/usr/bin/make
.PHONY: help install update build dchv dchr dchn

PACKAGE_NAME = homestead
PROJECT = homestead
PATH_PROJECT = $(DESTDIR)/var/www/$(PROJECT)

install: ##@system Install package. Don't run it manually!!!
	@echo Installing...
	install -d $(PATH_PROJECT)
	install -d $(PATH_PROJECT)/icons
	cp build/homestead.php $(PATH_PROJECT)/homestead.php
	cp build/homestead.phar $(PATH_PROJECT)/homestead.phar
	cp -r icons $(PATH_PROJECT)/
	cp -r config $(PATH_PROJECT)/
	git rev-parse --short HEAD > $(PATH_PROJECT)/_version
	git log --oneline --format=%B -n 1 HEAD | head -n 1 >> $(PATH_PROJECT)/_version
	git log --oneline --format="%at" -n 1 HEAD | xargs -I{} date -d @{} +%Y-%m-%d >> $(PATH_PROJECT)/_version

help:
	@perl -e '$(HELP_ACTION)' $(MAKEFILE_LIST)

build:
	@echo Building project
	@dh_clean ./build/ && \
	mkdir ./build && \
	GIT_VERSION=$$(git log --oneline --format=%B -n 1 HEAD | head -n 1) && \
	GIT_COMMIT=$$(git rev-parse --short HEAD) && \
	GIT_DATE=$$(git log --oneline --format="%at" -n 1 HEAD | xargs -I{} date -d @{} +%Y-%m-%d) && \
	composer install && \
	box compile --config=box.json && \
	cp homestead.php ./build/ && \
	cp homestead.phar ./build/ && \
	sed -i "s/%HOMESTEAD_VERSION%/Version $${GIT_VERSION}#$${GIT_COMMIT}\/$${GIT_DATE}/g" ./build/homestead.php
	@dpkg-buildpackage -rfakeroot --no-sign


phar:	##@build Compile PHAR file
	@box compile --config=box.json

pharlist:	##@build List of PHAR file
	@box info --list homestead.phar

dchr:		##@development Publish release
	@dch --controlmaint --release --distribution unstable

dchv:		##@development Append release
	@export DEBEMAIL="karel.wintersky@yandex.ru" && \
	export DEBFULLNAME="Karel Wintersky" && \
	echo "$(YELLOW)------------------ Previous version header: ------------------$(GREEN)" && \
	head -n 3 debian/changelog && \
	echo "$(YELLOW)--------------------------------------------------------------$(RESET)" && \
	read -p "Next version: " VERSION && \
	dch --controlmaint -v $$VERSION


# ------------------------------------------------
# Add the following 'help' target to your makefile, add help text after each target name starting with '\#\#'
# A category can be added with @category
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)
HELP_ACTION = \
	%help; while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-_]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; for (sort keys %help) { print "${WHITE}$$_:${RESET}\n"; \
	for (@{$$help{$$_}}) { $$sep = " " x (32 - length $$_->[0]); print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; }; \
	print "\n"; }

# -eof-