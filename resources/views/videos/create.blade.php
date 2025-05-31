<x-app-layout> {{-- Laravel Breezeのレイアウトを使用する場合 --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload New Video') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Title') }}</label>
                            <input id="title" name="title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('title') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="local_video_path" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Local Video File Path (Server Side)') }}</label>
                            <input id="local_video_path" name="local_video_path" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('local_video_path') }}">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Note: This field is for specifying a file path already on the server. For standard uploads from your computer, use the "Video File" field below.') }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="video_file" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Video File (Upload)') }}</label>
                            <input id="video_file" name="video_file" type="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 dark:file:bg-indigo-700 file:text-indigo-700 dark:file:text-indigo-300
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-600" accept="video/*">
                        </div>

                        <div class="mb-4">
                            <label for="thumbnail_file" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Thumbnail Image (Optional)') }}</label>
                            <input id="thumbnail_file" name="thumbnail_file" type="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 dark:file:bg-indigo-700 file:text-indigo-700 dark:file:text-indigo-300
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-600" accept="image/*" onchange="previewThumbnail(event)">
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Thumbnail Preview') }}</label>
                            <div class="mt-1 w-48 h-27 border border-gray-300 dark:border-gray-600 rounded-md flex items-center justify-center">
                                <img id="thumbnail_preview" src="#" alt="{{ __('Thumbnail Preview') }}" class="max-w-full max-h-full object-contain hidden">
                                <span id="thumbnail_placeholder" class="text-gray-400 dark:text-gray-500 text-sm">{{ __('No image selected') }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="visibility" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Visibility') }}</label>
                            <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="private" @if(old('visibility', 'private') == 'private') selected @endif>{{ __('Private') }}</option>
                                <option value="unlisted" @if(old('visibility') == 'unlisted') selected @endif>{{ __('Unlisted') }}</option>
                                <option value="public" @if(old('visibility') == 'public') selected @endif>{{ __('Public') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                {{ __('Upload Video') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewThumbnail(event) {
            const reader = new FileReader();
            const preview = document.getElementById('thumbnail_preview');
            const placeholder = document.getElementById('thumbnail_placeholder');
            reader.onload = function(){
                if (reader.readyState === 2) {
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>