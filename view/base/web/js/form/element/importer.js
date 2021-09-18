/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */
define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/form/element/abstract',
    'mage/backend/notification',
    'mage/translate',
    'jquery/file-uploader',
    'mage/adminhtml/tools'
], function ($, _, utils, validator, Element, notification, $t) {
    'use strict';

    const notifier = notification();

    return Element.extend({
        defaults: {
            aggregatedErrors: [],
            maxFileSize: false,
            allowedExtensions: false,
            previewTmpl: 'ui/form/element/uploader/preview',
            dropZone: '[data-role=drop-zone]',
            isLoading: false,
            uploaderConfig: {
                dataType: 'json',
                sequentialUploads: true,
                formData: {
                    'form_key': window.FORM_KEY
                }
            },
            tracks: {
                isLoading: true
            }
        },

        /**
         * Initializes file uploader plugin on provided input element.
         *
         * @param {HTMLInputElement} fileInput
         * @returns {FileUploader} Chainable.
         */
        initUploader: function (fileInput) {
            this.$fileInput = fileInput;

            _.extend(this.uploaderConfig, {
                dropZone: $(fileInput).closest(this.dropZone),
                add: this.onBeforeFileUpload.bind(this),
                done: this.onFileUploaded.bind(this),
                start: this.onLoadingStart.bind(this),
                stop: this.onLoadingStop.bind(this)
            });

            $(fileInput).fileupload(this.uploaderConfig);

            return this;
        },

        /**
         * Checks if provided file is allowed to be uploaded.
         *
         * @param {Object} file
         * @returns {Object} Validation result.
         */
        isFileAllowed: function (file) {
            let result;

            _.every([
                this.isExtensionAllowed(file),
                this.isSizeExceeded(file)
            ], function (value) {
                result = value;

                return value.passed;
            });

            return result;
        },

        /**
         * Checks if extension of provided file is allowed.
         *
         * @param {Object} file - File to be checked.
         * @returns {Boolean}
         */
        isExtensionAllowed: function (file) {
            return validator('validate-file-type', file.name, this.allowedExtensions);
        },

        /**
         * Checks if size of provided file exceeds
         * defined in configuration size limits.
         *
         * @param {Object} file - File to be checked.
         * @returns {Boolean}
         */
        isSizeExceeded: function (file) {
            return validator('validate-max-size', file.size, this.maxFileSize);
        },

        /**
         * Displays provided error message.
         *
         * @param {String} msg
         * @returns {FileUploader} Chainable.
         */
        notifierError: function (msg) {
            notifier.add({
                error: true,
                message: msg
            });
            return this;
        },

        /**
         * Add error message associated with filename for display when upload chain is complete
         *
         * @param {String} filename
         * @param {String} message
         */
        aggregateError: function (filename, message) {
            this.aggregatedErrors.push({
                filename: filename,
                message: message
            });
        },

        /**
         * Handler function which is supposed to be invoked when
         * file input element has been rendered.
         *
         * @param {HTMLInputElement} fileInput
         */
        onElementRender: function (fileInput) {
            this.initUploader(fileInput);
        },

        /**
         * Handler which is invoked prior to the start of a file upload.
         *
         * @param {Event} e - Event object.
         * @param {Object} data - File data that will be uploaded.
         */
        onBeforeFileUpload: function (e, data) {
            notifier.clear();

            let file = data.files[0],
                allowed = this.isFileAllowed(file),
                target = $(e.target);

            if (this.disabled()) {
                this.notifierError($t('The file upload field is disabled.'));

                return;
            }

            if (allowed.passed) {
                target.on('fileuploadsend', function (event, postData) {
                    postData.data.append('param_name', this.paramName);
                }.bind(data));

                target.fileupload('process', data).done(function () {
                    data.submit();
                });
            } else {
                this.aggregateError(file.name, allowed.message);

                // if all files in upload chain are invalid, stop callback is never called; this resolves promise
                if (this.aggregatedErrors.length === data.originalFiles.length) {
                    this.uploaderConfig.stop();
                }
            }
        },

        /**
         * Handler of the file upload complete event.
         *
         * @param {Event} e
         * @param {Object} data
         */
        onFileUploaded: function (e, data) {
            const result = data.result;
            if (result.error) {
                this.aggregateError(data.files[0].name, result.error);
            } else {
                notifier.add({
                    error: false,
                    message: $t('File imported successfully.')
                });
            }
        },

        /**
         * Load start event handler.
         */
        onLoadingStart: function () {
            this.isLoading = true;
        },

        /**
         * Load stop event handler.
         */
        onLoadingStop: function () {
            let aggregatedErrorMessages = [];

            this.isLoading = false;

            if (!this.aggregatedErrors.length) {
                return;
            }

            aggregatedErrorMessages.push(this.aggregatedErrors[0].message);

            this.notifierError(aggregatedErrorMessages.join(''));

            this.aggregatedErrors = [];
        }
    });
});
