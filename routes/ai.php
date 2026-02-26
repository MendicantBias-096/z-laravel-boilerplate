<?php

use App\Mcp\Servers\PermissionsServer;
use Laravel\Mcp\Facades\Mcp;

// Local — para Claude Code y agentes CLI
Mcp::local('permissions', PermissionsServer::class);

// Web — para clientes externos autenticados
// Mcp::web('/mcp/permissions', PermissionsServer::class)->middleware(['auth:sanctum']);
