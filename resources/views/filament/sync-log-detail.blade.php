<div class="space-y-4 text-sm">
    <div>
        <strong>Acción:</strong> {{ $log->action }}
    </div>
    <div>
        <strong>Fecha:</strong> {{ $log->created_at->format('d/m/Y H:i:s') }}
    </div>
    <div>
        <strong>Resultado:</strong>
        @if($log->success)
            <span class="text-green-600">Exitoso</span>
        @else
            <span class="text-red-600">Fallido</span>
        @endif
    </div>
    @if($log->error_message)
        <div>
            <strong>Error:</strong>
            <span class="text-red-600">{{ $log->error_message }}</span>
        </div>
    @endif
    <div>
        <strong>Request:</strong>
        <pre class="mt-1 rounded bg-gray-100 p-3 dark:bg-gray-800 overflow-x-auto text-xs">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    <div>
        <strong>Response:</strong>
        <pre class="mt-1 rounded bg-gray-100 p-3 dark:bg-gray-800 overflow-x-auto text-xs">{{ json_encode($log->response_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>
