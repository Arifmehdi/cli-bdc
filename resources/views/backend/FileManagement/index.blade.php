@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')

@section('content')
<style>
        .files {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }

        .file-item {
            text-align: center;
        }

        .file-item i {
            font-size: 48px;
            margin-bottom: 10px;
            color: #007bff;
            /* Blue color */
        }

        .file-item span {
            display: block;
            font-size: 14px;
            color: #333;
        }

        .context-menu {
            display: none;
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .context-menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .context-menu ul li {
            padding: 10px 20px;
            cursor: pointer;
        }

        .context-menu ul li:hover {
            background: #f0f0f0;
        }

        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            border: 1px solid #5bf635;
            /* color: #5bf635; */
            background-color: #5bf635;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
        }



        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
        <h2 class="text-center mb-4">File Management Demo</h2>
        <div class="row">
            <div class="col-md-2">
                <form id="upload-form" method="POST" enctype="multipart/form-data">
                    <label for="file-upload" class="custom-file-upload">
                        Upload your files
                    </label>
                    <input id="file-upload" type="file" accept=".jpg, .jpeg, .png, .gif, .pdf, .doc, .docx, .xls, .xlsx, .zip" multiple />
                </form>
            </div>
            <div class="col-md-10">
                <input type="button" value="{{ __('Back') }}" id="backButton" class="btn btn-success" onclick="back()" hidden />
                <input type="button" value="{{ __('Create File') }}" class="btn btn-success" onclick="openModal('createFileModal')" />
                <input type="button" value="{{ __('Create Folder') }}" id="folderCreate" class="btn btn-success" onclick="openModal('createFolderModal')">
                <input type="button" value="{{ __('Paste') }}" id="paste" class="btn btn-success" onclick="paste()" hidden>
                <input type="button" value="{{ __('Zip This Folder') }}" class="btn btn-success" onclick="zipFolder()">
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <div class="files">

                </div>
            </div>
        </div>



        <!-- Context Menu -->
        <div class="context-menu" id="contextMenu">
            <ul>
                <li onclick="extractFile()" id="extract" style="display:none;">Extract</li>
                <li onclick="openModal('renameModel')">Rename</li>
                <li onclick="cutAndCopy('cut')">Cut</li>
                <li onclick="cutAndCopy('copy')">Copy</li>
                <li onclick="paste()" id="paste" hidden>Paste</li>
                <li onclick="downloadItem()">Download</li>
                <li onclick="deleteItem()">Delete</li>
            </ul>
        </div>



    <!-- Create File Modal -->
    <div id="createFileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createFileModal')">&times;</span>
            <h2>Create File</h2>
            <input type="text" class="form-control" id="fileNameInput" placeholder="File Name">
            <textarea id="fileContentInput" class="form-control" placeholder="File Content"></textarea>
            <button onclick="createFile()" class="btn btn-success">Save</button>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div id="createFolderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createFolderModal')">&times;</span>
            <h2>Create Folder</h2>
            <input type="text" class="form-control" id="folderNameInput" placeholder="Folder Name">
            <button onclick="createFolder()" class="btn btn-success">Save</button>
        </div>
    </div>

    <!-- Rename Modal -->
    <div id="renameModel" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('renameModel')">&times;</span>
            <h2>Rename</h2>
            <input type="text" class="form-control" id="oldName" readonly hidden>
            <input type="text" class="form-control" id="newName" placeholder="Name">
            <button onclick="rename()" class="btn btn-success">Save</button>
        </div>
    </div>
@endsection




@push('js')
    <script src="{{ asset('assets/notify.min.js') }}"></script>

    <script>
        let currentPath = '';
        let copiedItem = null;
        let isCopy = null;
        let address = [];


        function getAllFilesAndFolders() {
            $.ajax({
                type: 'get',
                url: "{{ route('admin.file-manager.getAllFilesAndFolders') }}",
                data: { 'path': currentPath },
                success: function(data) {
                    console.log('Server response:', data);

                    // Empty the files div
                    $('.files').empty();

                    // Update current path
                    currentPath = data.path;
                    console.log('Updated path:', currentPath);

                    // Display directories and files
                    if (data.directories.length === 0 && data.files.length === 0) {
                        $('.files').append('<p class="empty-folder">File Uploaded <br> but images are show soon.</p>');
                    } else {
                        displayFilesAndFolders(data.directories, 'folder', data.path);
                        displayFilesAndFolders(data.files, 'file', data.path);
                    }

                    // Update address for back navigation
                    if (currentPath !== address.at(-1)) {
                        address.push(data.path);
                    }

                    // Show or hide back button
                    if (address.length > 1) {
                        $("#backButton").attr('hidden', false);
                    } else {
                        $("#backButton").attr('hidden', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error); // Debug AJAX errors
                }
            });
        }

        function back() {
            address.pop();
            currentPath = address.at(-1);
            getAllFilesAndFolders();
        }

        function displayFilesAndFolders(data, type, path) {
    // Define mapping of file extensions to icon classes
    const iconMappings = {
        'jpg': 'fas fa-image',
        'png': 'fas fa-image',
        'gif': 'fas fa-image',
        'bmp': 'fas fa-image',
        'zip': 'fas fa-file-archive',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel'
        // Add more mappings as needed
    };

    var html = '';
    data.map(item => {
        var iconClass = 'fas fa-folder';
        let name = item.replace(currentPath + '/', '');
        const isZipFile = item.split('.').pop().toLowerCase() === 'zip';
        
        // If it's a zip file, show the "Extract" option
        if (isZipFile) {
            $('#extract').show();
        } else {
            $('#extract').hide();
        }

        if (type !== 'folder') {
            iconClass = iconMappings[item.split('.').pop().toLowerCase()] || 'fas fa-file'; // Default to file icon
        }

        // Add the file/folder item to the HTML
        html += `<div class="file-item" data-name="${item}" data-type="${type}" data-path="${path}">
                    <i style="cursor:pointer" ${type === 'folder' ? `onclick="changePath('${item}')"` : ''} class="${iconClass}" oncontextmenu="showContextMenu(event, '${item}')"></i>
                    <span style="cursor:pointer" oncontextmenu="showContextMenu(event, '${item}')">${name}</span>
                </div>`;
    });

    // Append the files/folders to the DOM
    $('.files').append(html);
}


        function changePath(path) {
            currentPath = path;
            console.log('Changing path to:', currentPath); // Debug the path change
            getAllFilesAndFolders();
        }
        // Call getAllFilesAndFolders function when the page loads
        $(document).ready(function() {
            getAllFilesAndFolders();
        });

        // // Show context menu
        // function showContextMenu(event) {
        //     event.preventDefault();
        //     var contextMenu = document.getElementById('contextMenu');
        //     contextMenu.style.display = 'block';
        //     contextMenu.style.left = event.pageX + 'px';
        //     contextMenu.style.top = event.pageY + 'px';

        //     // Store the selected item data in the context menu element
        //     var selectedItem = $(event.target).closest('.file-item');
        //     $('.context-menu').data('selectedItem', selectedItem);
        // }

        // // Hide context menu
        // document.addEventListener('click', function(event) {
        //     closeContextMenu(event);
        // });


        function closeContextMenu(event) {
            var contextMenu = document.getElementById('contextMenu');
            if (!event || !event.target.closest('.context-menu')) {
                contextMenu.style.display = 'none';
                document.removeEventListener('click', closeContextMenu); // Remove the event listener
            }
        }


        function showContextMenu(event, fileName) {
            event.preventDefault(); // Prevent default context menu

            const fileType = fileName.split('.').pop().toLowerCase();

            if (fileType === 'zip') {
                var ee = 0;
                $('#extract').show();
                console.log('Extract option shown for:', fileName);
            } else {
                var ee = 1;
                $('#extract').hide();
                console.log('Extract option hidden for:', fileName);
            }
            // Set copiedItem to the selected file
            copiedItem = fileName;

            // Position the context menu
            const contextMenu = $('#contextMenu');
            contextMenu.css({
                top: `${event.pageY}px`,
                left: `${event.pageX}px`,
                display: 'block'
            });
        }

        function extractFile() {
            if (!copiedItem) {
                alert('No file selected for extraction.');
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.file-manager.extract') }}",
                data: {
                    file: currentPath + '/' + copiedItem // Combine path and file name
                },
                success: function (response) {
                    alert('File extracted successfully!');
                    getAllFilesAndFolders(); // Refresh the file list
                    location.reload()
                },
                error: function () {
                    alert('Failed to extract the file.');
                }
            });
        }
        // function showContextMenu(event, fileName) 
        // {
        //         event.preventDefault(); // Prevent the default context menu from showing

        //         // Hide the context menu by default
        //         $('#contextMenu').hide();

        //         // Show the context menu at the mouse position
        //         $('#contextMenu').css({
        //             top: event.pageY + 'px',
        //             left: event.pageX + 'px'
        //         }).show();

        //         // Check if the file is a zip file
        //         if (fileName.split('.').pop().toLowerCase() === 'zip') {
        //             $('#extract').show();  // Show the Extract option for zip files
        //         } else {
        //             $('#extract').hide();  // Hide the Extract option for other files
        //         }
        //     }

            // To hide the context menu when clicking outside of it
            $(document).click(function(e) {
                if (!$(e.target).closest('.context-menu').length && !$(e.target).closest('.file-item').length) {
                    $('#contextMenu').hide();
                }
            });


        // Function to open modal
        function openModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.style.display = "block";

            if (modalId == 'renameModel') {
                var selectedItem = $('.context-menu').data('selectedItem');
                var oldName = selectedItem.data('name');
                $("#oldName").val(oldName);
            }
        }

        // Function to close modal
        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.style.display = "none";
        }

        function createFile() {
            let data = {
                'fileName': $("#fileNameInput").val(),
                'fileContent': $("#fileContentInput").val(),
                'path': currentPath,
            };
            postAjax("{{ route('admin.file-manager.createFile') }}", data);
            closeModal('createFileModal');
        }

        function createFolder() {
            let data = {
                'folderName': $("#folderNameInput").val(),
                'path': currentPath,
            };
            postAjax("{{ route('admin.file-manager.createFolder') }}", data);
            closeModal('createFolderModal');
        }

        function rename() {
            let data = {
                'oldName': $("#oldName").val(),
                'newName': $("#newName").val(),
                'path': currentPath,
            };
            postAjax("{{ route('admin.file-manager.rename') }}", data);
            closeModal('renameModel');
        }


        function cutAndCopy(cutOrCopy) {

            var selectedItem = $('.context-menu').data('selectedItem');
            copiedItem = selectedItem.data('name');
            if (cutOrCopy == 'copy') {
                isCopy = 1;
            } else {
                isCopy = 0;
            }
            $("#paste").attr('hidden', false);
            closeContextMenu();
        }

        function paste() {
            let data = {
                'source': copiedItem,
                'destination': currentPath,
                'isCopy': isCopy,
            };
            postAjax("{{ route('admin.file-manager.paste') }}", data);
            $("#paste").attr('hidden', true);
            copiedItem = null;
            isCopy = null;
        }


        function zipFolder() {
            let data = {
                'folderToZip': currentPath,
            };
            postAjax("{{ route('admin.file-manager.zipFolder') }}", data);
        }

        function downloadItem() {
            var selectedItem = $('.context-menu').data('selectedItem');
            var name = selectedItem.data('name');
            var encodedName = encodeURIComponent(name); // Encode the file name 
            window.open("{{ route('admin.file-manager.download') }}?encoded_file_name=" + encodedName, "_blank");
        }



        function deleteItem() {
            var selectedItem = $('.context-menu').data('selectedItem');
            var name = selectedItem.data('name');
            let data = {
                'name': name,
            };
            postAjax("{{ route('admin.file-manager.delete') }}", data);
        }


        function postAjax(url, data) {
            data['_token'] = "{{ csrf_token() }}";
            data['path'] = currentPath;
            $.ajax({
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    getAllFilesAndFolders();
                    closeContextMenu();

                    location.reload();
                }
            });
        }


        $(document).ready(function() {
            $('#file-upload').on('change', function() {
                var formData = new FormData();
                var files = $(this)[0].files;

                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                // Add CSRF token directly to FormData
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('path', currentPath);
                $.ajax({
                    url: "{{ route('admin.file-manager.upload') }}",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        getAllFilesAndFolders();
                    },
                    error: function(error) {
                        console.log(error);
                        alert(error.responseJSON.message);
                        // Handle error
                    }
                });
            });
        });
    </script>


@endpush

<!-- @push('section')
<script>
    let currentPath = '';
    let address = [];

    function getAllFilesAndFolders(){
        $.ajax({
            type : 'get',
            url : "{{ route('admin.file-manager.getAllFilesAndFolders') }}",
            data:{
                'path': currentPath,
            },
            success: function(data){
                console.log(data);
                $('.files').empty();
                currentPath = data.path;
                displayFilesAndFolders(data.directories, 'folder', data.path);
                displayFilesAndFolders(data.files, 'files', data.path);

                if(currentPath != address.at(-1)){
                    address.push(data.path);
                }

                if (address.length > 1){
                    $('#backButton').attr('hidden', false);
                }
            }
        });
    }

    function back(){
        address.pop();
        currentPath = address.at(-1);
        getAllFilesAndFolders();
    }

    function displayFilesAndFolders(data, type, path) {
        const iconMappings = {
            'pdf': 'fa-file-pdf', // PDF icon
            'doc': 'fa-file-word', // Word document icon
            'docx': 'fa-file-word', // Word document icon
            'xls': 'fa-file-excel', // Excel icon
            'xlsx': 'fa-file-excel', // Excel icon
            'png': 'fa-file-image', // Image icon
            'jpg': 'fa-file-image', // Image icon
            'jpeg': 'fa-file-image', // Image icon
            'zip': 'fa-file-archive', // Archive icon
            'rar': 'fa-file-archive', // Archive icon
            // 'txt': 'fa-file-alt', // Text file icon
            'txt': 'far fa-file', // Text file icon
            'csv': 'fa-file-csv', // CSV file icon
            'mp4': 'fa-file-video', // Video file icon
            'mp3': 'fa-file-audio', // Audio file icon
        };

        let html = '';
        data.map(item => {
            let iconClass = 'fas fa-folder'; // Default to folder icon
            let name = item.replace(currentPath + '/', ''); // Adjust path to extract name

            if (type !== 'folder') {
                iconClass = iconMappings[item.split('.').pop().toLowerCase()] || 'fas fa-file'; // Default to generic file icon
            }

            html += `
                <div class="file-item" data-name="${item}" data-type="${type}">
                    <i class="${iconClass}" style="cursor:pointer" ${type === 'folder' ? `onclick="changePath('${item}')"` : ''}></i>
                    <span style="cursor:pointer" oncontextmenu="showContextMenu(event)">${name}</span>
                </div>
            `;
        });

        $('.files').append(html); // Corrected jQuery selector syntax
    }



    $(document).ready(function(){
        getAllFilesAndFolders();
    });

    function openModal(modalId)
    {
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
    }

    function closeModal(modalId)
    {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }

    function createFile()
    {
        url = "{{ route('admin.file-manager.createFile') }}"
        let data = {
            'fileName' : $('#fileNameInput').val(),
            'fileContent' : $('#fileContentInput').val(),
            'path' : currentPath,
        };
        postAjax(url, data)
        closeModal('createFileModal');
    }

    function createFolder()
    {
        url = "{{ route('admin.file-manager.createFolder') }}"
        let data = {
            'folderName' : $('#folderNameInput').val(),
            // 'fileContent' : $('#fileContentInput').val(),
            'path' : currentPath,
        };
        postAjax(url, data)
        closeModal('createFolderModal');
    }

    function changePath(path)
    {
        currentPath = path;
        getAllFilesAndFolders();
    }

    function postAjax(url, data)
    {
        data['_token'] = "{{ csrf_token() }}";
        data['path'] = currentPath;
        $.ajax({
            type : 'post',
            url : url,
            data: data,
            success: function(data){
                location.reload();
                getAllFilesAndFolders();
            }
        });
    }
</script>
@endpush -->