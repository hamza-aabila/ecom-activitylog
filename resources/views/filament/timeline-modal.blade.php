<div class="space-y-6">
    @if (!empty($timeline))
        <div class="space-y-6">
            @foreach ($timeline as $dayGroup)
                <div class="space-y-4">
                    <!-- Date Header -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ $dayGroup['formatted_date'] }}
                            </span>
                        </div>
                        <div class="flex-1 ml-4 border-t border-gray-200 dark:border-gray-700"></div>
                    </div>

                    <!-- Activities for this day -->
                    <div class="space-y-4">
                        @foreach ($dayGroup['activities'] as $activity)
                            <div class="relative flex items-start space-x-3">
                                <!-- Timeline Line -->
                                @if (!$loop->last || !$loop->parent->last)
                                    <div class="absolute left-4 top-8 -bottom-6 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                                @endif

                                <!-- Icon -->
                                <div class="relative flex-shrink-0">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        @switch($activity['meta']['color'])
                                            @case('success') bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400 @break
                                            @case('warning') bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400 @break
                                            @case('danger') bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400 @break
                                            @case('info') bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400 @break
                                            @case('primary') bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400 @break
                                            @default bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                                        @endswitch
                                    ">
                                        <x-dynamic-component :component="$activity['meta']['icon']" class="w-4 h-4" />
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                                        <!-- Header -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $activity['meta']['label'] }}
                                                </span>
                                                @if ($activity['subject'])
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $activity['subject']['type'] }} {{ $activity['subject']['label'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $activity['created_at']['human'] }}</span>
                                                <span>•</span>
                                                <span>{{ $activity['created_at']['formatted'] }}</span>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        @if ($activity['description'])
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                                {{ $activity['description'] }}
                                            </p>
                                        @endif

                                        <!-- Properties Changes -->
                                        @if (!empty($activity['properties']))
                                            <div class="space-y-2">
                                                @foreach ($activity['properties'] as $property)
                                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded dark:bg-gray-700">
                                                        <div class="flex items-center space-x-2">
                                                            @if ($property['icon'])
                                                                <x-dynamic-component :component="$property['icon']" class="w-3 h-3 text-gray-400" />
                                                            @endif
                                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $property['label'] }}:
                                                            </span>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                                                            {{ $property['formatted'] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Causer -->
                                        @if ($activity['causer'])
                                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-600">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center dark:bg-gray-600">
                                                        <x-heroicon-m-user class="w-3 h-3 text-gray-600 dark:text-gray-400" />
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        by {{ $activity['causer']['name'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Footer Info -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                Showing {{ $showing_count }} of {{ $total_activities }} activities
            </p>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <x-heroicon-o-clock class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" />
            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ __('ecom-activitylog::messages.no_activity') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('ecom-activitylog::messages.no_activity_description') }}
            </p>
        </div>
    @endif
</div>