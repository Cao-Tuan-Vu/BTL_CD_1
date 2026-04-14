@props([
    'messages' => null,
    'duration' => 5000,
])

@php
    $allowedTypes = ['success', 'error', 'warning'];

    if ($messages === null) {
        $messages = [];

        foreach ($allowedTypes as $type) {
            if (session()->has($type)) {
                $messages[] = [
                    'type' => $type,
                    'message' => session($type),
                ];
            }
        }

        if (session()->has('status')) {
            $messages[] = [
                'type' => 'success',
                'message' => session('status'),
            ];
        }

        if ($errors->any()) {
            foreach ($errors->all() as $errorMessage) {
                $messages[] = [
                    'type' => 'error',
                    'message' => $errorMessage,
                ];
            }
        }
    }

    $normalizedMessages = collect($messages)
        ->map(function ($toast) use ($allowedTypes) {
            if (is_string($toast)) {
                return [
                    'type' => 'success',
                    'message' => trim($toast),
                ];
            }

            $type = $toast['type'] ?? 'success';

            if (! in_array($type, $allowedTypes, true)) {
                $type = 'success';
            }

            return [
                'type' => $type,
                'message' => trim((string) ($toast['message'] ?? '')),
            ];
        })
        ->filter(fn (array $toast) => $toast['message'] !== '')
        ->values()
        ->all();

    $duration = max((int) $duration, 1000);
@endphp

<div class="toast-root" data-toast-root data-toast-duration="{{ $duration }}">
    <div class="toast-stack" data-toast-stack aria-live="polite" aria-atomic="false"></div>
    <script type="application/json" data-toast-payload>@json($normalizedMessages)</script>
</div>
