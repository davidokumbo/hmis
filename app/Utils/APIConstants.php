<?php 

namespace App\Utils;

class APIConstants
{
    // Success messages
    public const SUCCESS_MESSAGE = 'Operation completed successfully';
    public const RESOURCE_CREATED = 'Resource has been created successfully';

    // Error messages
    public const INVALID_REQUEST = 'Invalid request data provided';
    public const RESOURCE_NOT_FOUND = 'Requested resource not found';
    public const ROUTE_NOT_FOUND = 'Route not found';
    public const UNAUTHORIZED_ACCESS = 'You are not authorized to perform this action';
    public const ACCESS_DENIED = 'Access Denied';
    public const VALIDATION_ERROR = 'Validation error';
    public const METHOD_NOT_ALLOWED = 'Method not allowed';
    public const SERVER_ERROR = 'An unexpected error occurred. Please try again later';



    public const MESSAGE_ALREADY_EXISTS = 'Already exists';


    public const NAME_DEPARTMENT = 'Department';
}