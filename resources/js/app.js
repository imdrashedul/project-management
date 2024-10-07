import './bootstrap';
import 'summernote/dist/summernote-lite';
import 'summernote/dist/summernote-lite.css';
import $ from 'jquery';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import select2 from 'select2';
import 'select2/dist/css/select2.css';
import _ from 'lodash';

window.$ = $;
window.jQuery = $;
window._ = _;

select2();

axios.defaults.withCredentials = true;

window.initFileUpload = function ({ importRoute, acceptedMimes, maxFilesize }) {
    acceptedMimes = acceptedMimes.split(",");

    return {
        file: null,
        dragging: false,
        progress: 0,
        progressVisible: false,
        uploadedResponse: null,
        errorMessage: null,
        get fileExtensionMap() {
            return {
                'image/jpeg': 'JPG, JPEG',
                'image/png': 'PNG',
                'image/gif': 'GIF',
                'image/bmp': 'BMP',
                'image/webp': 'WEBP',
                'image/svg+xml': 'SVG',
                'text/plain': 'TXT',
                'text/html': 'HTML',
                'text/css': 'CSS',
                'text/javascript': 'JS',
                'text/csv': 'CSV',
                'application/pdf': 'PDF',
                'application/zip': 'ZIP',
                'application/x-rar-compressed': 'RAR',
                'application/vnd.ms-excel': 'XLS',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
                'application/vnd.ms-powerpoint': 'PPT',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
                'application/msword': 'DOC',
                'application/octet-stream': 'BIN',
                'audio/mpeg': 'MP3',
                'audio/wav': 'WAV',
                'audio/ogg': 'OGG',
                'audio/x-flac': 'FLAC',
                'video/mp4': 'MP4',
                'video/x-msvideo': 'AVI',
                'video/x-matroska': 'MKV',
                'video/ogg': 'OGG',
                'application/json': 'JSON',
                'application/x-www-form-urlencoded': 'URLEncoded',
                'application/x-tar': 'TAR',
                'application/x-7z-compressed': '7Z',
                'application/vnd.ms-fontobject': 'EOT',
                'application/vnd.google-apps.document': 'GDoc',
                'application/vnd.google-apps.presentation': 'GSlides',
                'application/vnd.google-apps.spreadsheet': 'GSheets',
                'application/x-shockwave-flash': 'SWF',
                'image/tiff': 'TIFF',
                'image/x-icon': 'ICO',
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow': 'PPSX',
                'application/vnd.ms-visio.drawing': 'VSD',
                'application/vnd.ms-visio.stencil': 'VSSX',
                'application/vnd.ms-visio.template': 'VST',
                'application/vnd.ms-project': 'MPP',
                'application/vnd.ms-access': 'MDB',
                'application/x-msdownload': 'EXE',
                'application/x-ms-application': 'APPLICATION',
                'application/x-ms-installer': 'MSI',
                'application/vnd.android.package-archive': 'APK',
                'application/vnd.apple.installer+xml': 'PKG',
                'application/x-apple-diskimage': 'DMG',
                'application/x-dosexec': 'COM',
                'application/x-msmediaview': 'MV',
                'application/x-pkcs12': 'P12, PFX',
                'application/x-pkcs7-certificates': 'P7B',
                'application/x-pkcs7-signature': 'P7S',
                'application/x-x509-ca-cert': 'CER',
                'application/x-xpinstall': 'XPI',
                'application/x-sql': 'SQL',
                'application/x-sh': 'SH',
                'application/x-bzip': 'BZ',
                'application/x-bzip2': 'BZ2',
                'application/x-tar-gz': 'TGZ',
                'application/x-compress': 'Z',
                'application/x-dvi': 'DVI',
                'application/x-latex': 'LATEX',
                'application/x-tex': 'TEX',
                'application/x-rar': 'RAR',
                'application/x-lzh-compressed': 'LZH',
                'application/x-apple-diskimage': 'DMG',
                'application/x-bittorrent': 'TORRENT',
                'application/vnd.mozilla.xul+xml': 'XUL',
                'application/vnd.oasis.opendocument.text': 'ODT',
                'application/vnd.oasis.opendocument.spreadsheet': 'ODS',
                'application/vnd.oasis.opendocument.presentation': 'ODP',
                'application/vnd.oasis.opendocument.graphics': 'ODG',
                'application/vnd.oasis.opendocument.formula': 'ODF',
                'application/vnd.sun.xml.writer': 'SXW',
                'application/vnd.sun.xml.calc': 'SXC',
                'application/vnd.sun.xml.impress': 'SXI',
                'application/vnd.sun.xml.draw': 'SXD',
                'application/vnd.sun.xml.math': 'SMF'
            };
        },
        get isFileValid() {
            return this.file && !this.errorMessage;
        },
        dragOver() {
            this.dragging = true;
        },
        dragLeave() {
            this.dragging = false;
        },
        dropFile(event) {
            this.dragging = false;
            this.uploadedResponse = null;
            this.validateFile(event.dataTransfer.files[0]);
        },
        fileChosen(event) {
            this.uploadedResponse = null;
            this.validateFile(this.$refs.fileInput.files[0]);
        },
        clearFile() {
            this.file = null;
            this.uploadedResponse = null;
            this.progress = 0;
            this.progressVisible = false;
            this.errorMessage = null;
        },
        formatBytesToReadable(sizeInBytes) {
            const units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            let index = 0;
            let size = parseFloat(sizeInBytes);

            while (size >= 1024 && index < units.length - 1) {
                size /= 1024;
                index++;
            }

            return `${size % 1 === 0 ? size : size.toFixed(2)} ${units[index]}`;
        },
        formatMbToReadable(sizeInMB) {
            const units = ['MB', 'GB', 'TB'];
            let index = 0;
            let size = parseFloat(sizeInMB);

            while (size >= 1024 && index < units.length - 1) {
                size /= 1024;
                index++;
            }

            return `${this.formatNumber(size)} ${units[index]}`;
        },
        validateFile(file) {
            this.file = file;
            this.errorMessage = null;
            const maxFileSizeBytes = parseFloat(maxFilesize) * 1024 * 1024;

            if (!acceptedMimes.includes(file.type)) {
                const validExtensions = acceptedMimes.map(mime => this.fileExtensionMap[mime]).join(', ');
                this.errorMessage = `Invalid file type. Accepted types: ${validExtensions}`;
                this.file = null;
                return;
            }

            if (parseFloat(file.size) > maxFileSizeBytes) {
                this.errorMessage = `File size exceeds the maximum limit of ${this.formatMbToReadable(maxFilesize)}. Given ${this.formatBytesToReadable(file.size)}`;
                this.file = null;
                return;
            }

            this.file.readableSize = this.formatBytesToReadable(file.size);
            this.file.typeName = this.fileExtensionMap[file.type] ?? file.type;
        },
        formatNumber(value) {
            return value % 1 === 0 ? value : value.toFixed(2);
        },
        uploadFile() {
            if (!this.isFileValid) return;

            let formData = new FormData();
            formData.append('file', this.file);

            this.progressVisible = true;

            axios.post(importRoute, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (event) => {
                    this.progress = Math.round((event.loaded * 100) / event.total);
                }
            }).then((response) => {
                this.uploadedResponse = response;
                this.file = null;
                this.progress = 0;
                this.errorMessage = null;
            }).catch((error) => {
                this.uploadedResponse = {
                    error: error
                };
            }).finally(() => {
                this.progressVisible = false;
            });
        }
    };
}

window.initPushNotification = function (userId, channel) {
    return {
        notifications: [],
        visible: [],
        init() {
            if (userId) {
                window.Echo.private(`${channel ?? 'App.Models.User'}.${userId}`).notification((notification) => {
                    this.addNotification(notification);
                });
            }
        },
        addNotification(notification) {
            this.notifications.push(notification);
            this.visible.push(true);
            setTimeout(() => this.closeNotification(this.notifications.length - 1), 300000);
        },
        closeNotification(index) {
            this.visible[index] = false;
            setTimeout(() => {
                this.notifications.splice(index, 1);
                this.visible.splice(index, 1);
            }, 500);
        },
    };
}

window.initSummernote = element => {
    $(element).summernote({
        height: 300,
        placeholder: 'Write details...',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
};

window.initFlatpickr = element => {
    flatpickr(element, {
        enableTime: true,
        dateFormat: 'Y-m-d H:i:ss',
        placeholder: 'Enter date and time'
    });
}

window.initSelect2 = (element, config, options) => {
    const dependency = config.dependency ? $(`select[x-ref="${config.dependency}"]`) : null;
    const defaultValue = () => {
        if (_.has(config, 'default') && _.has(config, 'optionApi') && config.default != '' && config.optionApi != '') {
            $.ajax({
                url: config.optionApi,
                dataType: 'json',
                data: {
                    id: config.default
                },
                success: function (data) {
                    $(element).append(
                        new Option(data.name, data.id, true, true)
                    ).trigger('change');
                },
                error: function (xhr, status, error) { }
            });
        }
    };

    $(element).select2({
        ajax: {
            cache: true,
            delay: 250,
            url: config.api,
            dataType: 'json',
            data: function (params) {
                const query = {
                    search: params.term,
                    page: params.page || 1
                }

                if (dependency && dependency.length) {
                    query[config.dependency] = dependency.val();
                }

                return query;
            }
        },
        placeholder: config.placeholder ?? "Select an option",
        allowClear: config.allowClear ?? false,
        width: '100%',
    }).val(options).trigger('change');

    defaultValue();
};

window.Alpine = Alpine;
Alpine.start();
