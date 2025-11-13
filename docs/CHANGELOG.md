 Dino Library Changelog

# 📦 Changelog

All notable changes to the Dino Library project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## \[Unreleased\]

*   Initial project structure and core components
*   ServiceContainer with PSR-11 implementation
*   LibraryManager for simple service registration
*   ConfigHandler for hierarchical configuration management
*   FactoryInterface for customizable service creation
*   ServiceProviderInterface for modular service registration
*   ConfigurableInterface for configurable services
*   ServiceInterface for service lifecycle management
*   Comprehensive exception hierarchy
*   Unit tests for all core components
*   Usage examples and documentation

## \[1.0.0\] - 2024-XX-XX

### ✨ Added

*   Core architecture with PSR compliance
*   Service management system
*   Configuration system with dot notation
*   Contracts & interfaces
*   Error handling hierarchy
*   Testing & quality assurance
*   Documentation suite

## \[1.1.0\] - 2025-11-11

### ✨ Added

*   ConfigLoader support for JSON and YAML formats
*   CachedConfigLoader with PSR-16 compatible caching
*   ArrayCache implementation for in-memory testing
*   EventDispatcherInterface and EventDispatcher for service lifecycle events
*   ServiceLifecycleEvents constants
*   Unit tests for CachedConfigLoader and event system
*   Examples: config-handler-demo.php, cached-config-loader-demo.php
*   Documentation: config handlers, event system

## \[1.1.1\] - 2025-11-12

### ✨ Added

*   Validation System integrated into ConfigHandler
*   ValidatorInterface contract for custom validators
*   Built-in validators: RequiredValidator, TypeValidator, RangeValidator, RegexValidator
*   Examples: config-validation-demo.php, config-validation-explained.md
*   Documentation updates: Guides/config-handlers.md, API-Reference/ConfigHandler.md, API-Reference/ValidatorInterface.md

### 🛠️ Changed

*   ConfigHandler::set() now integrates validation

## \[1.2.1\] - 2025-11-13

### ✨ Added

*   Context-aware exception system with standardized error codes
*   ErrorMessageFormatter utility for structured error output
*   Integration of ValidatorRegistry with ConfigHandler
*   Advanced rule parsing for validators (required, type, range, regex)
*   New exceptions: ConfigValidationException, ConfigNotFoundException, ConfigParserException, ServiceResolutionException, ServiceNotFoundException, ValidatorNotFoundException
*   Example files: error-handling-demo.php, advanced-validation-demo.php
*   Documentation: Error Handling Guide, Advanced Validation Guide

### 🛠️ Changed

*   Replaced legacy exceptions (ValidationException, ServiceException, ContainerException, ConfigurationException, InvalidConfigFormatException) with new context-aware exceptions
*   Updated ConfigHandler to throw ConfigValidationException with detailed context
*   Updated ServiceContainer and DependencyResolver to use ServiceResolutionException and ServiceNotFoundException

### 🧹 Removed

*   Deprecated legacy exception classes from `src/Exceptions/`
*   Old validation references replaced with new context-aware system