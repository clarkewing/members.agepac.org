<template>
    <div ref="trixWrapper" class="trix-wrapper">
        <input :id="'trix' + _uid" type="hidden" :name="name" :value="value">

        <trix-toolbar :id="'trixToolbar' + _uid">
            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar">
                <div class="btn-group mb-2 mr-2" role="group" aria-label="Text tools">
                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="bold" data-trix-key="b" :title="lang.bold">
                        <svg class="bi bi-type-bold" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 001.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13H8.21zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="italic" data-trix-key="i" :title="lang.italic">
                        <svg class="bi bi-type-italic" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.991 11.674L9.53 4.455c.123-.595.246-.71 1.347-.807l.11-.52H7.211l-.11.52c1.06.096 1.128.212 1.005.807L6.57 11.674c-.123.595-.246.71-1.346.806l-.11.52h3.774l.11-.52c-1.06-.095-1.129-.211-1.006-.806z"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="underline" data-trix-key="u" :title="lang.underline">
                        <svg class="bi bi-type-underline" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.313 3.136h-1.23V9.54c0 2.105 1.47 3.623 3.917 3.623s3.917-1.518 3.917-3.623V3.136h-1.23v6.323c0 1.49-.978 2.57-2.687 2.57-1.709 0-2.687-1.08-2.687-2.57V3.136z"/>
                            <path fill-rule="evenodd" d="M12.5 15h-9v-1h9v1z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="strike" :title="lang.strike">
                        <svg class="bi bi-type-strikethrough" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.527 13.164c-2.153 0-3.589-1.107-3.705-2.81h1.23c.144 1.06 1.129 1.703 2.544 1.703 1.34 0 2.31-.705 2.31-1.675 0-.827-.547-1.374-1.914-1.675L8.046 8.5h3.45c.468.437.675.994.675 1.697 0 1.826-1.436 2.967-3.644 2.967zM6.602 6.5H5.167a2.776 2.776 0 01-.099-.76c0-1.627 1.436-2.768 3.48-2.768 1.969 0 3.39 1.175 3.445 2.85h-1.23c-.11-1.08-.964-1.743-2.25-1.743-1.23 0-2.18.602-2.18 1.607 0 .31.083.581.27.814z"/>
                            <path fill-rule="evenodd" d="M15 8.5H1v-1h14v1z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="href" data-trix-action="link" data-trix-key="k" :title="lang.link">
                        <svg class="bi bi-link-45deg" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.715 6.542L3.343 7.914a3 3 0 104.243 4.243l1.828-1.829A3 3 0 008.586 5.5L8 6.086a1.001 1.001 0 00-.154.199 2 2 0 01.861 3.337L6.88 11.45a2 2 0 11-2.83-2.83l.793-.792a4.018 4.018 0 01-.128-1.287z"/>
                            <path d="M5.712 6.96l.167-.167a1.99 1.99 0 01.896-.518 1.99 1.99 0 01.518-.896l.167-.167A3.004 3.004 0 006 5.499c-.22.46-.316.963-.288 1.46z"/>
                            <path d="M6.586 4.672A3 3 0 007.414 9.5l.775-.776a2 2 0 01-.896-3.346L9.12 3.55a2 2 0 012.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 00-4.243-4.243L6.586 4.672z"/>
                            <path d="M10 9.5a2.99 2.99 0 00.288-1.46l-.167.167a1.99 1.99 0 01-.896.518 1.99 1.99 0 01-.518.896l-.167.167A3.004 3.004 0 0010 9.501z"/>
                        </svg>
                    </button>
                </div>

                <div class="btn-group mb-2 mr-2" role="group" aria-label="Block tools">
                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="heading1" :title="lang.heading1">
                        <svg class="bi bi-type-h1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.637 13V3.669H7.379V7.62H2.758V3.67H1.5V13h1.258V8.728h4.62V13h1.259zm5.329 0V3.669h-1.244L10.5 5.316v1.265l2.16-1.565h.062V13h1.244z"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="heading2" :title="lang.heading2">
                        <svg class="bi bi-type-h2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.638 13V3.669H6.38V7.62H1.759V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.022-6.733v-.048c0-.889.63-1.668 1.716-1.668.957 0 1.675.608 1.675 1.572 0 .855-.554 1.504-1.067 2.085l-3.513 3.999V13H15.5v-1.094h-4.245v-.075l2.481-2.844c.875-.998 1.586-1.784 1.586-2.953 0-1.463-1.155-2.556-2.919-2.556-1.941 0-2.966 1.326-2.966 2.74v.049h1.223z"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="heading3" :title="lang.heading3">
                        <svg class="bi bi-type-h3" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.637 13V3.669H6.379V7.62H1.758V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.625-4.272h1.018c1.142 0 1.935.67 1.949 1.674.013 1.005-.78 1.737-2.01 1.73-1.08-.007-1.853-.588-1.935-1.32H9.108c.069 1.327 1.224 2.386 3.083 2.386 1.935 0 3.343-1.155 3.309-2.789-.027-1.51-1.251-2.16-2.037-2.249v-.068c.704-.123 1.764-.91 1.723-2.229-.035-1.353-1.176-2.4-2.954-2.385-1.873.006-2.857 1.162-2.898 2.358h1.196c.062-.69.711-1.299 1.696-1.299.998 0 1.695.622 1.695 1.525.007.922-.718 1.592-1.695 1.592h-.964v1.074z"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="quote" :title="lang.quote">
                        <svg class="bi bi-blockquote-left" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2 3.5a.5.5 0 01.5-.5h11a.5.5 0 010 1h-11a.5.5 0 01-.5-.5zm5 3a.5.5 0 01.5-.5h6a.5.5 0 010 1h-6a.5.5 0 01-.5-.5zm0 3a.5.5 0 01.5-.5h6a.5.5 0 010 1h-6a.5.5 0 01-.5-.5zm-5 3a.5.5 0 01.5-.5h11a.5.5 0 010 1h-11a.5.5 0 01-.5-.5z" clip-rule="evenodd"/>
                            <path d="M3.734 6.352a6.586 6.586 0 00-.445.275 1.94 1.94 0 00-.346.299 1.38 1.38 0 00-.252.369c-.058.129-.1.295-.123.498h.282c.242 0 .431.06.568.182.14.117.21.29.21.521a.697.697 0 01-.187.463c-.12.14-.289.21-.503.21-.336 0-.577-.108-.721-.327C2.072 8.619 2 8.328 2 7.969c0-.254.055-.485.164-.692.11-.21.242-.398.398-.562.16-.168.33-.31.51-.428.18-.117.33-.213.451-.287l.211.352zm2.168 0a6.588 6.588 0 00-.445.275 1.94 1.94 0 00-.346.299c-.113.12-.199.246-.257.375a1.75 1.75 0 00-.118.492h.282c.242 0 .431.06.568.182.14.117.21.29.21.521a.697.697 0 01-.187.463c-.12.14-.289.21-.504.21-.335 0-.576-.108-.72-.327-.145-.223-.217-.514-.217-.873 0-.254.055-.485.164-.692.11-.21.242-.398.398-.562.16-.168.33-.31.51-.428.18-.117.33-.213.451-.287l.211.352z"/>
                        </svg>
                    </button>
                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="bullet" :title="lang.bullets">
                        <svg class="bi bi-list-ul" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 11.5a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0-4a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0-4a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm-3 1a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-attribute="number" :title="lang.numbers">
                        <svg class="bi bi-list-ol" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 11.5a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0-4a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0-4a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5z" clip-rule="evenodd"/>
                            <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 01-.492.594v.033a.615.615 0 01.569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 00-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/>
                        </svg>
                    </button>
                </div>

                <div class="btn-group mb-2 ml-sm-auto" role="group" aria-label="History tools">
                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-action="undo" data-trix-key="z" :title="lang.undo">
                        <svg class="bi bi-arrow-counterclockwise" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.83 6.706a5 5 0 00-7.103-3.16.5.5 0 11-.454-.892A6 6 0 112.545 5.5a.5.5 0 11.91.417 5 5 0 109.375.789z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M7.854.146a.5.5 0 00-.708 0l-2.5 2.5a.5.5 0 000 .708l2.5 2.5a.5.5 0 10.708-.708L5.707 3 7.854.854a.5.5 0 000-.708z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <button type="button" class="btn btn-secondary px-2" tabindex="-1"
                            data-trix-action="redo" data-trix-key="shift+z" :title="lang.redo">
                        <svg class="bi bi-arrow-clockwise" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3.17 6.706a5 5 0 017.103-3.16.5.5 0 10.454-.892A6 6 0 1013.455 5.5a.5.5 0 00-.91.417 5 5 0 11-9.375.789z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M8.147.146a.5.5 0 01.707 0l2.5 2.5a.5.5 0 010 .708l-2.5 2.5a.5.5 0 11-.707-.708L10.293 3 8.147.854a.5.5 0 010-.708z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="trix-dialogs" data-trix-dialogs>
                <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
                    <div class="trix-dialog__link-fields">
                        <input type="url" name="href" class="trix-input trix-input--dialog" :placeholder="lang.urlPlaceholder" :aria-label="lang.url" required data-trix-input>
                        <div class="trix-button-group">
                            <input type="button" class="trix-button trix-button--dialog" :value="lang.link" data-trix-method="setAttribute">
                            <input type="button" class="trix-button trix-button--dialog" :value="lang.unlink" data-trix-method="removeAttribute">
                        </div>
                    </div>
                </div>
            </div>
        </trix-toolbar>

        <trix-editor
            ref="editor"
            :input="'trix' + _uid"
            :toolbar="'trixToolbar' + _uid"
            :class="['form-control', error ? 'is-invalid' : '']"
            @trix-change="change"
            :placeholder="placeholder">
        </trix-editor>

        <div class="invalid-feedback" v-text="error"></div>
    </div>
</template>

<script>
    import Tribute from 'tributejs';
    import Trix from 'trix';

    export default {
        props: ['name', 'value', 'placeholder', 'error'],

        data() {
            return {
                lang: Trix.config.lang,
                tribute: new Tribute({
                    trigger: '@',
                    allowSpaces: true,
                    menuShowMinLength: 2,

                    lookup: 'name',
                    fillAttr: 'username',

                    containerClass: 'tribute-container dropdown-menu',
                    itemClass: 'dropdown-item d-flex align-items-center',
                    selectClass: 'hover',
                    noMatchTemplate: '',

                    menuItemTemplate: function (item) {
                        return '<img src="' + item.original.avatar_path + '" class="rounded-circle cover mr-2" style="width: 1.5em; height: 1.5em;">'
                            + '<span>' + item.string + '</span>';
                    },

                    // TODO: Fix this so it works with Trix, some info here: https://github.com/basecamp/trix/issues/284#issuecomment-500579603
                    // selectTemplate: function (item) {
                    //     return '<span class="mention">@' + item.original.username + '</span>';
                    // },

                    values: (query, cb) => {
                        this.remoteSearch(query, users => cb(users));
                    }
                })
            }
        },

        methods: {
            change({target}) {
                this.$emit('input', target.value)
            },

            remoteSearch(query, cb) {
                axios.get('/api/users', {
                    params: {
                        name: query
                    }
                })
                    .then(function (response) {
                        cb(response.data);
                    })
                    .catch(function (error) {
                        cb([]);
                    });
            }
        },

        watch: {
            value(val) {
                if (val === '') {
                    this.$refs.editor.value = '';
                }
            }
        },

        created() {
            // Hacky workaround for bug where hitting backspace in an empty editor, destroys it.
            document.onkeydown = (event) => {
                const isBackspace = (event.keyCode || event.charCode) === 8;

                if (isBackspace && document.activeElement === this.$refs.trixWrapper && this.value === '') {
                    return false;
                }
            };

            Trix.config.textAttributes.underline = {
                style: {"textDecoration": "underline"},
                inheritable: true,
                parser: function (element) {
                    var style = window.getComputedStyle(element);
                    return style.textDecoration.includes("underline");
                }
            };
            Trix.config.blockAttributes.heading2 = {
                tagName: 'h2',
                terminal: true,
                breakOnReturn: true,
                group: false
            };
            Trix.config.blockAttributes.heading3 = {
                tagName: 'h3',
                terminal: true,
                breakOnReturn: true,
                group: false
            };
        },

        mounted() {
            this.tribute.attach(this.$refs.editor);
        },

        beforeDestroy() {
            this.tribute.detach(this.$refs.editor);
        }
    }
</script>
