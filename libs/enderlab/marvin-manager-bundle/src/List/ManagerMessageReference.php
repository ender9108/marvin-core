<?php

namespace EnderLab\MarvinManagerBundle\List;

use EnderLab\ToolsBundle\Service\ListTrait;

class ManagerMessageReference
{
    use ListTrait;

    public const string REQUEST_INSTALL_DOCKER = 'request_install_docker';
    public const string REQUEST_DECLARE_DOCKER = 'request_declare_docker';
    public const string REQUEST_UPDATE_DOCKER = 'request_update_docker';
    public const string REQUEST_DELETE_DOCKER = 'request_delete_docker';
    public const string REQUEST_START_DOCKER = 'request_start_docker';
    public const string REQUEST_RESTART_DOCKER = 'request_restart_docker';
    public const string REQUEST_STOP_DOCKER = 'request_stop_docker';
    public const string REQUEST_BUILD_DOCKER = 'request_build_docker';
    public const string REQUEST_EXECUTE_COMMAND_DOCKER = 'request_execute_command_docker';
    public const string REQUEST_DISCOVER_DOCKER = 'request_discover_docker';

    public const string RESPONSE_INSTALL_DOCKER = 'response_install_docker';
    public const string RESPONSE_DECLARE_DOCKER = 'response_declare_docker';
    public const string RESPONSE_UPDATE_DOCKER = 'response_update_docker';
    public const string RESPONSE_DELETE_DOCKER = 'response_delete_docker';
    public const string RESPONSE_START_DOCKER = 'response_start_docker';
    public const string RESPONSE_RESTART_DOCKER = 'response_restart_docker';
    public const string RESPONSE_STOP_DOCKER = 'response_stop_docker';
    public const string RESPONSE_BUILD_DOCKER = 'response_build_docker';
    public const string RESPONSE_EXECUTE_COMMAND_DOCKER = 'response_execute_command_docker';
}