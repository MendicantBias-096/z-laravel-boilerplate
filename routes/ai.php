<?php

declare(strict_types=1);

use App\Mcp\Servers\BoilerplateServer;
use Laravel\Mcp\Facades\Mcp;

// Local (stdio) — for Claude Code / CLI agents
Mcp::local('boilerplate', BoilerplateServer::class);
