.PHONY: ${TARGETS}
.DEFAULT_GOAL := help

help:
	@echo "\033[1;36mAVAILABLE COMMANDS :\033[0m"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' Makefile

##@ Base commands
install: ## Install dependencies
	@composer install

##@ Quality commands
test: ## Run all tests
	@vendor/bin/phpunit

phpstan: ## Run PHPStan
	@composer --working-dir tools/phpstan install
	@tools/phpstan/vendor/bin/phpstan analyse

cs-lint: ## Lint all files
	@composer --working-dir tools/php-cs-fixer install
	@tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix CS using PHP-CS
	@composer --working-dir tools/php-cs-fixer install
	@tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
