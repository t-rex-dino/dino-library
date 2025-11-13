 Dino Library API Reference - v1.2.1

# 📖 API Reference - Dino Library v1.2.1

This section provides detailed documentation for all public classes, interfaces, and exceptions in Dino Library.

## Core Components

*   [ConfigHandler](ConfigHandler.html)
*   [ServiceContainer](ServiceContainer.html)
*   [LibraryManager](LibraryManager.html)
*   [DependencyResolver](DependencyResolver.html)
*   [ParameterResolver](ParameterResolver.html)

## Contracts

*   [FactoryInterface](FactoryInterface.html)
*   [ServiceProviderInterface](ServiceProviderInterface.html)
*   [ServiceInterface](ServiceInterface.html)
*   [ConfigurableInterface](ConfigurableInterface.html)
*   [ValidatorInterface](ValidatorInterface.html)

## Validation System

*   [ValidatorRegistry](ValidatorRegistry.html)
*   [RequiredValidator](RequiredValidator.html)
*   [TypeValidator](TypeValidator.html)
*   [RangeValidator](RangeValidator.html)
*   [RegexValidator](RegexValidator.html)

## Exception Hierarchy (New in v1.2.1)

All exceptions inherit from `ContextAwareException` and provide structured context data.

*   [ContextAwareException](ContextAwareException.html)
*   [ConfigValidationException](ConfigValidationException.html)
*   [ConfigNotFoundException](ConfigNotFoundException.html)
*   [ConfigParserException](ConfigParserException.html)
*   [ServiceNotFoundException](ServiceNotFoundException.html)
*   [ServiceResolutionException](ServiceResolutionException.html)
*   [ServiceFactoryException](ServiceFactoryException.html)
*   [CircularDependencyException](CircularDependencyException.html)
*   [UnresolvableParameterException](UnresolvableParameterException.html)
*   [InterfaceNotBoundException](InterfaceNotBoundException.html)
*   [LazyLoadingException](LazyLoadingException.html)
*   [ServiceWrapperException](ServiceWrapperException.html)
*   [ValidatorNotFoundException](ValidatorNotFoundException.html)

## Utilities

*   [ErrorMessageFormatter](ErrorMessageFormatter.html)
*   [LazyServiceWrapper](LazyServiceWrapper.html)
*   [ServiceTagRegistry](ServiceTagRegistry.html)

## Notes

*   Legacy exceptions have been removed in v1.2.1.
*   All validators now throw `ConfigValidationException` with detailed context.
*   Service resolution errors are standardized under `ServiceResolutionException`.