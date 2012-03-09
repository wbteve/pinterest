﻿/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function() {
    var a = (function() {
        var f = {
            jY: 'B23B',
            _: {},
            status: 'unloaded',
            basePath: (function() {
                var i = window.CKFINDER_BASEPATH || '';
                if (!i) {
                    var j = document.getElementsByTagName('script');
                    for (var k = 0; k < j.length; k++) {
                        var l = j[k].src.match(/(^|.*[\\\/])CKFINDER(?:_basic)?(?:_v2)?(?:_source)?.js(?:\?.*)?$/i);
                        if (l) {
                            i = l[1];
                            break;
                        }
                    }
                }
                if (i.indexOf('://') == -1) if (i.indexOf('/') === 0) i = location.href.match(/^.*?:\/\/[^\/]*/)[0] + i;
                else i = location.href.match(/^[^\?]*\/(?:)/)[0] + i;
                return i;
            })(),
            getUrl: function(i) {
                if (i.indexOf('://') == -1 && i.indexOf('/') !== 0) i = this.basePath + i;
                if (this.jY && i.charAt(i.length - 1) != '/') i += (i.indexOf('?') >= 0 ? '&': '?') + 't=' + this.jY;
                return i;
            }
        },
        g = window.CKFINDER_GETURL;
        if (g) {
            var h = f.getUrl;
            f.getUrl = function(i) {
                return g.call(f, i) || h.call(f, i);
            };
        }
        return f;
    })();
    function b(f) {
        return a.instances[f];
    };
    var c = {
        callback: 1,
        selectThumbnailActionFunction: 1,
        selectActionFunction: 1
    };
    a.jd = function() {
        var h = this;
        var f = {};
        for (var g in h) {
            if (!h.hasOwnProperty(g)) continue;
            if (typeof h[g] == 'function' && !c[g]) continue;
            f[g] = h[g];
        }
        if (h.callback) f.callback = h.callback;
        return f;
    };
    a.lj = function(f) {
        var i = this;
        f = f || i.basePath;
        var g = '';
        if (!f || f.length === 0) f = CKFinder.DEFAULT_basePath;
        if (f.substr(f.length - 1, 1) != '/') f += '/';
        f += 'ckfinder.html';
        var h;
        if (i.hh) {
            h = i.hh;
            if (typeof h == 'function') h = h.toString().match(/function ([^(]+)/)[1];
            g += '?action=js&amp;func=' + h;
        }
        if (i.jx) {
            g += g ? '&amp;': '?';
            g += 'data=' + encodeURIComponent(i.jx);
        }
        if (i.disableThumbnailSelection) {
            g += g ? '&amp;': '?';
            g += 'dts=1';
        } else if (i.lH || i.hh) {
            h = i.lH || i.hh;
            if (typeof h == 'function') h = h.toString().match(/function ([^(]+)/)[1];
            g += g ? '&amp;': '?';
            g += 'thumbFunc=' + h;
            if (i.nm) g += '&amp;tdata=' + encodeURIComponent(i.nm);
            else if (!i.lH && i.jx) g += '&amp;tdata=' + encodeURIComponent(i.jx);
        }
        if (i.startupPath) {
            g += g ? '&amp;': '?';
            g += 'start=' + encodeURIComponent(i.startupPath + (i.startupFolderExpanded ? ':1': ':0'));
        }
        if (!i.rememberLastFolder) {
            g += g ? '&amp;': '?';
            g += 'rlf=0';
        }
        if (i.id) {
            g += g ? '&amp;': '?';
            g += 'id=' + encodeURIComponent(i.id);
        }
        if (i.skin) {
            g += g ? '&amp;': '?';
            g += 'skin=' + encodeURIComponent(i.skin);
        }
        return f + g;
    };
    function d(f) {
        var i = this;
        i.id = f.name;
        var g = f.ax.getDocument().getWindow().$,
        h = a.oC.getWindow().$;
        i.inPopup = !!(g && g.opener);
        i.inIframe = !i.inPopup && g != h.top && g.frameElement.nodeName.toLowerCase() == 'iframe';
        i.inFrame = !i.inPopup && g != h.top && g.frameElement.nodeName.toLowerCase() == 'frame';
        i.inUrlPopup = !!(i.inPopup && h.opener);
    };
    function e(f, g, h) {
        g.on('appReady',
        function(i) {
            i.aF();
            f.document = g.document.$;
            f.folders = g.folders;
            f.files = g.aG['filesview.filesview'][0].data().files;
            f.basketFiles = g.basketFiles;
            f.resourceTypes = g.resourceTypes;
            f.connector = g.connector;
            f.lang = g.lang;
            f.langCode = g.langCode;
            f.config = g.config;
            g.aG['foldertree.foldertree'][0].on('afterAddFolder',
            function(j) {
                j.aF();
                if (h) h(f);
            },
            f);
        },
        f, null, 999);
    };
    d.prototype = {
        _: {},
        addFileContextMenuOption: function(f, g, h) {
            var i = b(this.id),
            j = 'FileContextMenu_' + f.command;
            i.bD(j, {
                exec: function(m) {
                    var n = m.aG['filesview.filesview'][0].tools.dH();
                    g(m.cg, n);
                }
            });
            f.command = j;
            if (!f.group) f.group = 'file1';
            i.gp(j, f);
            var k = i.aG['filesview.filesview'];
            for (var l = 0; l < k.length; l++) k[l].on('beforeContextMenu',
            function o(m) {
                if (h) {
                    var n = h(this.tools.dH());
                    if (n) m.data.bj[j] = n == -1 ? a.aY: a.aS;
                } else m.data.bj[j] = a.aS;
            });
        },
        disableFileContextMenuOption: function(f, g) {
            var h = b(this.id),
            i = g ? 'FileContextMenu_' + f: f,
            j = h.aG['filesview.filesview'],
            k = [];
            for (var l = 0; l < j.length; l++) {
                var m = k.push(function o(n) {
                    delete n.data.bj[i];
                });
                j[l].on('beforeContextMenu', k[m - 1]);
            }
            return function() {
                for (var n = 0; n < k.length; n++) h.aG['foldertree.foldertree'][n].aF('beforeContextMenu', k[n]);
            };
        },
        addFolderContextMenuOption: function(f, g, h) {
            var i = b(this.id),
            j = 'FolderContextMenu_' + f.command;
            i.bD(j, {
                exec: function(m) {
                    g(m.cg, m.aV);
                }
            });
            f.command = j;
            if (!f.group) f.group = 'folder1';
            i.gp(j, f);
            var k = i.aG['foldertree.foldertree'];
            for (var l = 0; l < k.length; l++) k[l].on('beforeContextMenu',
            function o(m) {
                if (h) {
                    var n = h(this.app.aV);
                    if (n) m.data.bj[j] = n == -1 ? a.aY: a.aS;
                } else m.data.bj[j] = a.aS;
            });
        },
        disableFolderContextMenuOption: function(f, g) {
            var h = b(this.id),
            i = g ? 'FolderContextMenu_' + f: f,
            j = h.aG['foldertree.foldertree'],
            k = [];
            for (var l = 0; l < j.length; l++) {
                var m = k.push(function o(n) {
                    delete n.data.bj[i];
                });
                j[l].on('beforeContextMenu', k[m - 1]);
            }
            return function() {
                for (var n = 0; n < k.length; n++) h.aG['foldertree.foldertree'][n].aF('beforeContextMenu', k[n]);
            };
        },
        getSelectedFile: function() {
            return b(this.id).aG['filesview.filesview'][0].tools.dH();
        },
        getSelectedFolder: function() {
            return b(this.id).aV;
        },
        setUiColor: function(f) {
            return b(this.id).setUiColor(f);
        },
        openDialog: function(f, g) {
            var j = this;
            var h = new a.dom.document(window.document).eD(),
            i = b(j.id).document.getWindow();
            if (j.inFrame || j.inPopup || j.inIframe) a.document = b(j.id).document;
            return b(j.id).openDialog(f, g, h);
        },
        openMsgDialog: function(f, g) {
            b(this.id).msgDialog(f, g);
        },
        openConfirmDialog: function(f, g, h) {
            b(this.id).fe(f, g, h);
        },
        openInputDialog: function(f, g, h, i) {
            b(this.id).hs(f, g, h, i);
        },
        addTool: function(f) {
            return b(this.id).plugins.tools.addTool(f);
        },
        addToolPanel: function(f) {
            return b(this.id).plugins.tools.addToolPanel(f);
        },
        removeTool: function(f) {
            b(this.id).plugins.tools.removeTool(f);
        },
        showTool: function(f) {
            b(this.id).plugins.tools.showTool(f);
        },
        hideTool: function(f) {
            b(this.id).plugins.tools.hideTool(f);
        },
        getResourceType: function(f) {
            return b(this.id).getResourceType(f);
        },
        log: function(f) {
            a.log.apply(a.log, arguments);
        },
        getLog: function() {
            return a.mZ();
        },
        emptyBasket: function() {
            b(this.id).execCommand('TruncateBasket');
        },
        replaceUploadForm: function(f, g) {
            var h = b(this.id);
            h.aG['formpanel.formpanel'][0].on('beforeUploadFileForm',
            function(i) {
                if (i.data.step != 2) return;
                i.cancel(true);
                var j = this.data(),
                k = i.data.folder;
                try {
                    if (j.dc == 'upload') this.oW('requestUnloadForm',
                    function() {
                        this.app.cS('upload').bR(a.aS);
                    });
                    else {
                        if (this.data().dc) this.oW('requestUnloadForm');
                        this.oW('requestLoadForm', {
                            html: f,
                            command: 'upload'
                        });
                        g && g();
                    }
                } catch(l) {
                    this.oW('failedUploadFileForm', i.data);
                    this.oW('afterUploadFileForm', i.data);
                    throw a.ba(l);
                }
            });
            return {
                hide: function() {
                    h.oW('requestUnloadForm',
                    function() {
                        h.cS('upload').bR(a.aS);
                    });
                }
            };
        },
        refreshOpenedFolder: function() {
            var f = b(this.id),
            g = f.aG['filesview.filesview'][0].tools.currentFolder();
            f.oW('requestSelectFolder', {
                folder: g
            });
        },
        closePopup: function() {
            if (!this.inPopup) return;
            b(this.id).ax.getDocument().getWindow().$.close();
        }
    };
    (function() {
        window.CKFinder = function(f, g) {
            if (f) for (var h in f) {
                if (!f.hasOwnProperty(h)) continue;
                if (typeof f[h] == 'function' && h != 'callback') continue;
                this[h] = f[h];
            }
            this.callback = g;
        };
        CKFinder.prototype = {
            create: function(f) {
                var g = 'ckf' + Math.random().toString().substr(2, 4);
                document.write('<span id="' + g + '"></span>');
                f = a.tools.extend(a.jd.call(this), f, true);
                var h = a.replace(g, f, CKFinder);
                this.lN = h.cg;
                return h.cg;
            },
            appendTo: function(f, g) {
                g = a.tools.extend(a.jd.call(this), g, true);
                var h = a.appendTo(f, g, CKFinder);
                this.lN = h.cg;
                return h.cg;
            },
            replace: function(f, g) {
                g = a.tools.extend(a.jd.call(this), g, true);
                var h = a.replace(f, g, CKFinder);
                this.lN = h.cg;
                return h.cg;
            },
            popup: function(f, g) {
                var o = this;
                f = f || '80%';
                g = g || '70%';
                if (typeof f == 'string' && f.length > 1 && f.substr(f.length - 1, 1) == '%') f = parseInt(window.screen.width * parseInt(f, 10) / 100, 10);
                if (typeof g == 'string' && g.length > 1 && g.substr(g.length - 1, 1) == '%') g = parseInt(window.screen.height * parseInt(g, 10) / 100, 10);
                if (f < 200) f = 200;
                if (g < 200) g = 200;
                var h = parseInt((window.screen.height - g) / 2, 10),
                i = parseInt((window.screen.width - f) / 2, 10),
                j = 'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,jy=yes,width=' + f + ',height=' + g + ',top=' + h + ',left=' + i,
                k = a.env.webkit ? 'about:blank': '',
                l = window.open(k, 'CKFinderpopup', j, true);
                if (!l) return false;
                o.width = o.height = '100%';
                var m = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html><head><title>CKFinder 2</title><style type="text/css">body, html, iframe, #ckfinder { margin: 0; padding: 0; border: 0; width: 100%; height: 100%; overflow: hidden; }</style></head><body></body></html>',
                n = new a.dom.document(l.document);
                n.$.open();
                if (a.env.isCustomDomain()) n.$.domain = window.document.domain;
                n.$.write(m);
                n.$.close();
                try {
                    l.moveTo(i, h);
                    l.resizeTo(f, g);
                    l.focus();
                    return o.appendTo(n.bH());
                } catch(p) {
                    return o.appendTo(n.bH());
                }
                return false;
            }
        };
        CKFinder._ = {};
        CKFinder.lang = {};
        CKFinder.version = '2.0.2';
        CKFinder.revision = 'UNKNOWN';
        CKFinder.addPlugin = function(f, g, h) {
            var i = {
                bM: h || []
            };
            if (typeof g == 'function') g = {
                appReady: g
            };
            for (var j in g) {
                if (!g.hasOwnProperty(j)) continue;
                if (j != 'connectorInitialized' && j != 'uiReady') i[j] = g[j];
            }
            i.bz = function(k) {
                if (g.connectorInitialized) k.on('connectorInitialized',
                function(l) {
                    var m = g.connectorInitialized;
                    if (m) m.call(m, k.cg, l.data.xml);
                },
                null, null, 1000);
                if (g.uiReady) k.on('uiReady',
                function() {
                    var l = g.uiReady;
                    l.call(l, k.cg);
                },
                null, null, 1000);
                if (g.appReady) k.on('appReady',
                function() {
                    var l = g.appReady;
                    l.call(l, k.cg);
                },
                null, null, 1000);
            };
            a.plugins.add(f, i);
        };
        CKFinder.getPluginPath = function(f) {
            return a.plugins.getPath(f);
        };
        CKFinder.addExternalPlugin = function(f, g, h) {
            a.plugins.tR(f, g, h);
        };
        CKFinder.setPluginLang = function(f, g, h) {
            a.plugins.rX(f, g, h);
        };
        CKFinder.dialog = {
            add: function(f, g) {
                if (typeof g == 'function') g = a.tools.override(g,
                function(h) {
                    return function(i) {
                        return h(i.cg);
                    };
                });
                a.dialog.add(f, g);
            }
        };
        CKFinder.tools = {};
        CKFinder.env = {};
        CKFinder.dom = {};
        CKFinder.create = function(f, g, h, i, j) {
            var k;
            if (f !== null && typeof f === 'object') {
                k = new CKFinder();
                for (var l in f) k[l] = f[l];
            } else {
                k = new CKFinder();
                k.basePath = f;
                if (g) k.width = g;
                if (h) k.height = g;
                if (i) k.selectActionFunction = i;
                if (j) k.callback = j;
            }
            return k.create();
        };
        CKFinder.popup = function(f, g, h, i, j) {
            var k;
            if (f !== null && typeof f === 'object') {
                k = new CKFinder();
                for (var l in f) k[l] = f[l];
            } else {
                k = new CKFinder();
                k.basePath = f;
                if (i) k.selectActionFunction = i;
                if (j) k.callback = j;
            }
            return k.popup(g, h);
        };
        CKFinder.setupFCKeditor = function(f, g, h, i) {
            var j;
            if (g !== null && typeof g === 'object') {
                j = new CKFinder();
                for (var k in g) {
                    j[k] = g[k];
                    if (k == 'width') {
                        var l = j[k] || 800;
                        if (typeof l == 'string' && l.length > 1 && l.substr(l.length - 1, 1) == '%') l = parseInt(window.screen.width * parseInt(l, 10) / 100, 10);
                        f.Config.LinkBrowserWindowWidth = l;
                        f.Config.ImageBrowserWindowWidth = l;
                        f.Config.FlashBrowserWindowWidth = l;
                    } else if (k == 'height') {
                        var m = j[k] || 600;
                        if (typeof m == 'string' && m.length > 1 && m.substr(m.length - 1, 1) == '%') m = parseInt(window.screen.height * parseInt(m, 10) / 100, 10);
                        f.Config.LinkBrowserWindowHeight = m;
                        f.Config.ImageBrowserWindowHeight = m;
                        f.Config.FlashBrowserWindowHeight = m;
                    }
                }
            } else {
                j = new CKFinder();
                j.basePath = g;
            }
            var n = j.basePath;
            if (n.substr(0, 1) != '/' && n.indexOf('://') == -1) n = document.location.pathname.substring(0, document.location.pathname.lastIndexOf('/') + 1) + n;
            n = a.lj.call(j, n);
            var o = n.indexOf('?') !== -1 ? '&amp;': '?';
            f.Config.LinkBrowserURL = n;
            f.Config.ImageBrowserURL = n + o + 'type=' + (h || 'Images');
            f.Config.FlashBrowserURL = n + o + 'type=' + (i || 'Flash');
            var p = n.substring(0, 1 + n.lastIndexOf('/'));
            f.Config.LinkUploadURL = p + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=Files';
            f.Config.ImageUploadURL = p + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=' + (h || 'Images');
            f.Config.FlashUploadURL = p + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=' + (i || 'Flash');
        };
        CKFinder.setupCKEditor = function(f, g, h, i) {
            if (f === null) {
                for (var j in CKEDITOR.instances) CKFinder.setupCKEditor(CKEDITOR.instances[j], g, h, i);
                CKEDITOR.on('instanceCreated',
                function(r) {
                    CKFinder.setupCKEditor(r.editor, g, h, i);
                });
                return;
            }
            var k;
            if (g !== null && typeof g === 'object') {
                k = new CKFinder();
                for (var l in g) {
                    k[l] = g[l];
                    if (l == 'width') {
                        var m = k[l] || 800;
                        if (typeof m == 'string' && m.length > 1 && m.substr(m.length - 1, 1) == '%') m = parseInt(window.screen.width * parseInt(m, 10) / 100, 10);
                        f.config.filebrowserWindowWidth = m;
                    } else if (l == 'height') {
                        var n = k[l] || 600;
                        if (typeof n == 'string' && n.length > 1 && n.substr(n.length - 1, 1) == '%') n = parseInt(window.screen.height * parseInt(n, 10) / 100, 10);
                        f.config.filebrowserWindowHeight = m;
                    }
                }
            } else {
                k = new CKFinder();
                k.basePath = g;
            }
            var o = k.basePath;
            if (o.substr(0, 1) != '/' && o.indexOf('://') == -1) o = document.location.pathname.substring(0, document.location.pathname.lastIndexOf('/') + 1) + o;
            o = a.lj.call(k, o);
            var p = o.indexOf('?') !== -1 ? '&amp;': '?';
            f.config.filebrowserBrowseUrl = o;
            f.config.filebrowserImageBrowseUrl = o + p + 'type=' + (h || 'Images');
            f.config.filebrowserFlashBrowseUrl = o + p + 'type=' + (i || 'Flash');
            var q = o.substring(0, 1 + o.lastIndexOf('/'));
            f.config.filebrowserUploadUrl = q + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=Files';
            f.config.filebrowserImageUploadUrl = q + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=' + (h || 'Images');
            f.config.filebrowserFlashUploadUrl = q + 'core/connector/' + CKFinder.config.connectorLanguage + '/connector.' + CKFinder.config.connectorLanguage + '?command=QuickUpload&type=' + (i || 'Flash');
        };
    })();
    if (!a.event) {
        a.event = function() {};
        a.event.du = function(f, g) {
            var h = a.event.prototype;
            for (var i in h) {
                if (f[i] == undefined) f[i] = h[i];
            }
        };
        a.event.prototype = (function() {
            var f = function(h) {
                var i = h.kk && h.kk() || h._ || (h._ = {});
                return i.cC || (i.cC = {});
            },
            g = function(h) {
                this.name = h;
                this.dF = [];
            };
            g.prototype = {
                mi: function(h) {
                    for (var i = 0, j = this.dF; i < j.length; i++) {
                        if (j[i].gg == h) return i;
                    }
                    return - 1;
                }
            };
            return {
                on: function(h, i, j, k, l) {
                    var m = f(this),
                    n = m[h] || (m[h] = new g(h));
                    if (n.mi(i) < 0) {
                        var o = n.dF;
                        if (!j) j = this;
                        if (isNaN(l)) l = 10;
                        var p = this,
                        q = function(s, t, u, v) {
                            var w = {
                                name: h,
                                jN: this,
                                application: s,
                                data: t,
                                jO: k,
                                stop: u,
                                cancel: v,
                                aF: function() {
                                    p.aF(h, i);
                                }
                            };
                            i.call(j, w);
                            return w.data;
                        };
                        q.gg = i;
                        q.nT = l;
                        for (var r = o.length - 1; r >= 0; r--) {
                            if (o[r].nT <= l) {
                                o.splice(r + 1, 0, q);
                                return;
                            }
                        }
                        o.unshift(q);
                    }
                },
                oW: (function() {
                    var h = false,
                    i = function() {
                        h = true;
                    },
                    j = false,
                    k = function(l) {
                        j = l ? 2 : true;
                    };
                    return function w(l, m, n, o) {
                        if (typeof m == 'function') {
                            o = m;
                            m = null;
                        } else if (typeof n == 'function') {
                            o = n;
                            n = null;
                        }
                        if (l != 'mousemove') a.log('[EVENT] ' + l, m, o);
                        var p = f(this)[l],
                        q = h,
                        r = j;
                        h = j = false;
                        if (p) {
                            var s = p.dF;
                            if (s.length) {
                                s = s.slice(0);
                                for (var t = 0; t < s.length; t++) {
                                    var u = s[t].call(this, n, m, i, k);
                                    if (typeof u != 'undefined') m = u;
                                    if (h || j && j != 2) break;
                                }
                            }
                        }
                        var v = j || (typeof m == 'undefined' ? false: !m || typeof m.result == 'undefined' ? m: m.result);
                        if (typeof o === 'function' && j != 2) v = o.call(this, j, m) || v;
                        h = q;
                        j = r;
                        return v;
                    };
                })(),
                cr: function(h, i, j) {
                    var k = this.oW(h, i, j);
                    delete f(this)[h];
                    return k;
                },
                aF: function(h, i) {
                    var j = f(this)[h];
                    if (j) {
                        var k = j.mi(i);
                        if (k >= 0) j.dF.splice(k, 1);
                    }
                },
                mF: function() {
                    var h = f(this);
                    for (var i = 0; i < h.length; i++) h[i].dF = [];
                },
                rC: function(h) {
                    var i = f(this)[h];
                    return i && i.dF.length > 0;
                }
            };
        })();
    }
    if (!a.application) {
        a.kZ = 0;
        a.fc = 1;
        a.qE = 2;
        a.application = function(f, g, h, i) {
            var j = this;
            j._ = {
                kw: f,
                ax: g
            };
            j.ff = h || a.kZ;
            a.event.call(j);
            j.iI(i);
        };
        a.application.replace = function(f, g, h) {
            var i = f;
            if (typeof i != 'object') {
                i = document.getElementById(f);
                if (!i) {
                    var j = 0,
                    k = document.getElementsByName(f);
                    while ((i = k[j++]) && i.tagName.toLowerCase() != 'textarea') {}
                }
                if (!i) throw '[CKFINDER.application.replace] The ax with id or name "' + f + '" was not found.';
            }
            return new a.application(g, i, a.fc, h);
        };
        a.application.appendTo = function(f, g, h) {
            if (typeof f != 'object') {
                f = document.getElementById(f);
                if (!f) throw '[CKFINDER.application.appendTo] The ax with id "' + f + '" was not found.';
            }
            return new a.application(g, f, a.qE, h);
        };
        a.application.prototype = {
            iI: function() {
                var f = a.application.eb || (a.application.eb = []);
                f.push(this);
            },
            oW: function(f, g, h) {
                return a.event.prototype.oW.call(this, f, g, this, h);
            },
            cr: function(f, g, h) {
                return a.event.prototype.cr.call(this, f, g, this, h);
            }
        };
        a.event.du(a.application.prototype, true);
    }
    if (!a.env) {
        a.env = (function() {
            var f = navigator.userAgent.toLowerCase(),
            g = window.opera,
            h = {
                ie:
                /*@cc_on!@*/
                false,
                opera: !!g && g.version,
                webkit: f.indexOf(' applewebkit/') > -1,
                air: f.indexOf(' adobeair/') > -1,
                mac: f.indexOf('macintosh') > -1,
                quirks: document.compatMode == 'BackCompat',
                isCustomDomain: function() {
                    return this.ie && document.domain != window.location.hostname;
                }
            };
            h.gecko = navigator.product == 'Gecko' && !h.webkit && !h.opera;
            var i = 0;
            if (h.ie) {
                i = parseFloat(f.match(/msie (\d+)/)[1]);
                h.ie8 = !!document.documentMode;
                h.ie8Compat = document.documentMode == 8;
                h.ie7Compat = i == 7 && !document.documentMode || document.documentMode == 7;
                h.ie6Compat = i < 7 || h.quirks;
            }
            if (h.gecko) {
                var j = f.match(/rv:([\d\.]+)/);
                if (j) {
                    j = j[1].split('.');
                    i = j[0] * 10000 + (j[1] || 0) * 100 + +(j[2] || 0);
                }
            }
            if (h.opera) i = parseFloat(g.version());
            if (h.air) i = parseFloat(f.match(/ adobeair\/(\d+)/)[1]);
            if (h.webkit) i = parseFloat(f.match(/ applewebkit\/(\d+)/)[1]);
            h.version = i;
            h.isCompatible = h.ie && i >= 6 || h.gecko && i >= 10801 || h.opera && i >= 9.5 || h.air && i >= 1 || h.webkit && i >= 522 || false;
            h.cssClass = 'browser_' + (h.ie ? 'ie': h.gecko ? 'gecko': h.opera ? 'opera': h.air ? 'air': h.webkit ? 'webkit': 'unknown');
            if (h.quirks) h.cssClass += ' browser_quirks';
            if (h.ie) {
                h.cssClass += ' browser_ie' + (h.version < 7 ? '6': h.version >= 8 ? '8': '7');
                if (h.quirks) h.cssClass += ' browser_iequirks';
            }
            if (h.gecko && i < 10900) h.cssClass += ' browser_gecko18';
            return h;
        })();
        CKFinder.env = a.env;
    }
    var f = a.env;
    var g = f.ie;
    if (a.status == 'unloaded')(function() {
        a.event.du(a);
        a.dO = function() {
            if (a.status != 'basic_ready') {
                a.dO.qr = true;
                return;
            }
            delete a.dO;
            var i = document.createElement('script');
            i.type = 'text/javascript';
            i.src = a.basePath + 'ckfinder.js';
            document.getElementsByTagName('head')[0].appendChild(i);
        };
        a.mS = 0;
        a.uQ = 'ckfinder';
        a.uM = true;
        var h = function(i, j, k, l) {
            if (f.isCompatible) {
                if (a.dO) a.dO();
                var m = k(i, j, l);
                a.add(m);
                return m;
            }
            return null;
        };
        a.replace = function(i, j, k) {
            return h(i, j, a.application.replace, k);
        };
        a.appendTo = function(i, j, k) {
            return h(i, j, a.application.appendTo, k);
        };
        a.add = function(i) {
            var j = this._.io || (this._.io = []);
            j.push(i);
        };
        a.uL = function() {
            var i = document.getElementsByTagName('textarea');
            for (var j = 0; j < i.length; j++) {
                var k = null,
                l = i[j],
                m = l.name;
                if (!l.name && !l.id) continue;
                if (typeof arguments[0] == 'string') {
                    var n = new RegExp('(?:^| )' + arguments[0] + '(?:$| )');
                    if (!n.test(l.className)) continue;
                } else if (typeof arguments[0] == 'function') {
                    k = {};
                    if (arguments[0](l, k) === false) continue;
                }
                this.replace(l, k);
            }
        };
        (function() {
            var i = function() {
                var j = a.dO,
                k = a.mS;
                a.status = 'basic_ready';
                if (j && j.qr) j();
                else if (k) setTimeout(function() {
                    if (a.dO) a.dO();
                },
                k * 1000);
            };
            if (window.addEventListener) window.addEventListener('load', i, false);
            else if (window.attachEvent) window.attachEvent('onload', i);
        })();
        a.status = 'basic_loaded';
    })();
    a.dom = {};
    CKFinder.dom = a.dom;
    var h = a.dom;
    a.ajax = (function() {
        var i = function() {
            if (!g || location.protocol != 'file:') try {
                return new XMLHttpRequest();
            } catch(n) {}
            try {
                return new ActiveXObject('Msxml2.XMLHTTP');
            } catch(o) {}
            try {
                return new ActiveXObject('Microsoft.XMLHTTP');
            } catch(p) {}
            return null;
        },
        j = function(n) {
            return n.readyState == 4 && (n.status >= 200 && n.status < 300 || n.status == 304 || n.status === 0 || n.status == 1223);
        },
        k = function(n) {
            if (j(n)) return n.responseText;
            return null;
        },
        l = function(n) {
            if (j(n)) {
                var o = n.responseXML,
                p = new a.xml(o && o.firstChild && o.documentElement && o.documentElement.nodeName != 'parsererror' ? o: n.responseText.replace(/^[^<]+/, '').replace(/[^>]+$/, ''));
                if (p && p.mq && p.mq.documentElement && p.mq.documentElement.nodeName != 'parsererror' && p.mq.documentElement.nodeName != 'html' && p.mq.documentElement.nodeName != 'br') return p;
            }
            var q = a.eq || a.jt,
            r = n.responseText;
            if (/text\/plain/.test(n.getResponseHeader('Content-Type'))) {
                r = a.tools.htmlEncode(r);
                r = r.replace(/\n/g, '<br>');
                r = '<div style="width:600px; overflow:scroll"><font>' + r + '</font></div>';
            }
            q.msgDialog(q.lang.SysErrorDlgTitle, r);
            return {};
        },
        m = function(n, o, p, q) {
            var r = !!o;
            a.log('[AJAX] POST ' + n);
            var s = i();
            if (!s) return null;
            if (!q) s.open('GET', n, r);
            else s.open('POST', n, r);
            if (r) s.onreadystatechange = function() {
                if (s.readyState == 4) {
                    o(p(s));
                    s = null;
                }
            };
            if (q) {
                s.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                s.send(q);
            } else s.send(null);
            return r ? '': p(s);
        };
        return {
            load: function(n, o, p) {
                return m(n, o, k, p);
            },
            loadXml: function(n, o, p) {
                return m(n, o, l, p);
            }
        };
    })();
    CKFinder.ajax = a.ajax;
    (function() {
        var i = [];
        a.tools = {
            arrayCompare: function(j, k) {
                if (!j && !k) return true;
                if (!j || !k || j.length != k.length) return false;
                for (var l = 0; l < j.length; l++) {
                    if (j[l] != k[l]) return false;
                }
                return true;
            },
            clone: function(j) {
                var k;
                if (j && j instanceof Array) {
                    k = [];
                    for (var l = 0; l < j.length; l++) k[l] = this.clone(j[l]);
                    return k;
                }
                if (j === null || typeof j != 'object' || j instanceof String || j instanceof Number || j instanceof Boolean || j instanceof Date) return j;
                k = new j.constructor();
                for (var m in j) {
                    var n = j[m];
                    k[m] = this.clone(n);
                }
                return k;
            },
            extend: function(j) {
                var k = arguments.length,
                l, m;
                if (typeof(l = arguments[k - 1]) == 'boolean') k--;
                else if (typeof(l = arguments[k - 2]) == 'boolean') {
                    m = arguments[k - 1];
                    k -= 2;
                }
                for (var n = 1; n < k; n++) {
                    var o = arguments[n];
                    for (var p in o) {
                        if (l === true || j[p] == undefined) if (!m || p in m) j[p] = o[p];
                    }
                }
                return j;
            },
            prototypedCopy: function(j) {
                var k = function() {};
                k.prototype = j;
                return new k();
            },
            isArray: function(j) {
                return ! !j && j instanceof Array;
            },
            cssStyleToDomStyle: (function() {
                var j = document.createElement('div').style,
                k = typeof j.cssFloat != 'undefined' ? 'cssFloat': typeof j.styleFloat != 'undefined' ? 'styleFloat': 'float';
                return function(l) {
                    if (l == 'float') return k;
                    else return l.replace(/-./g,
                    function(m) {
                        return m.substr(1).toUpperCase();
                    });
                };
            })(),
            htmlEncode: function(j) {
                var k = function(o) {
                    var p = new h.ax('span');
                    p.setText(o);
                    return p.getHtml();
                },
                l = k('\n').toLowerCase() == '<br>' ?
                function(o) {
                    return k(o).replace(/<br>/gi, '\n');
                }: k,
                m = k('>') == '>' ?
                function(o) {
                    return l(o).replace(/>/g, '&gt;');
                }: l,
                n = k('  ') == '&nbsp; ' ?
                function(o) {
                    return m(o).replace(/&nbsp;/g, ' ');
                }: m;
                this.htmlEncode = n;
                return this.htmlEncode(j);
            },
            getNextNumber: (function() {
                var j = 0;
                return function() {
                    return++j;
                };
            })(),
            override: function(j, k) {
                return k(j);
            },
            setTimeout: function(j, k, l, m, n) {
                if (!n) n = window;
                if (!l) l = n;
                return n.setTimeout(function() {
                    if (m) j.apply(l, [].concat(m));
                    else j.apply(l);
                },
                k || 0);
            },
            trim: (function() {
                var j = /(?:^[ \t\n\r]+)|(?:[ \t\n\r]+$)/g;
                return function(k) {
                    return k ? k.replace(j, '') : null;
                };
            })(),
            ltrim: (function() {
                var j = /^[ \t\n\r]+/g;
                return function(k) {
                    return k ? k.replace(j, '') : null;
                };
            })(),
            rtrim: (function() {
                var j = /[ \t\n\r]+$/g;
                return function(k) {
                    return k ? k.replace(j, '') : null;
                };
            })(),

            indexOf: Array.prototype.indexOf ?
            function(j, k) {
                return j.indexOf(k);
            }: function(j, k) {
                for (var l = 0, m = j.length; l < m; l++) {
                    if (j[l] === k) return l;
                }
                return - 1;
            },
            bind: function(j, k) {
                return function() {
                    return j.apply(k, arguments);
                };
            },
            createClass: function(j) {
                var k = j.$,
                l = j.base,
                m = j.vd || j._,
                n = j.ej,
                o = j.statics;
                if (m) {
                    var p = k;
                    k = function() {
                        var t = this;
                        var q = t._ || (t._ = {});
                        for (var r in m) {
                            var s = m[r];
                            q[r] = typeof s == 'function' ? a.tools.bind(s, t) : s;
                        }
                        p.apply(t, arguments);
                    };
                }
                if (l) {
                    k.prototype = this.prototypedCopy(l.prototype);
                    k.prototype['constructor'] = k;
                    k.prototype.base = function() {
                        this.base = l.prototype.base;
                        l.apply(this, arguments);
                        this.base = arguments.callee;
                    };
                }
                if (n) this.extend(k.prototype, n, true);
                if (o) this.extend(k, o, true);
                return k;
            },
            addFunction: function(j, k) {
                return i.push(function() {
                    j.apply(k || this, arguments);
                }) - 1;
            },
            callFunction: function(j) {
                var k = i[j];
                return k.apply(window, Array.prototype.slice.call(arguments, 1));
            },
            cssLength: (function() {
                var j = /^\d+(?:\.\d+)?$/;
                return function(k) {
                    return k + (j.test(k) ? 'px': '');
                };
            })(),
            repeat: function(j, k) {
                return new Array(k + 1).join(j);
            },
            deepCopy: function(j) {
                var k = {};
                if (typeof j == 'object') {
                    if (typeof j.length != 'undefined') k = [];
                    for (var l in j) {
                        if (j[l] === null) k[l] = j[l];
                        else if (typeof j[l] == 'object') k[l] = a.tools.deepCopy(j[l]);
                        else if (typeof j[l] == 'string') k[l] = j[l];
                        else if (typeof j[l] == 'number') k[l] = j[l];
                        else if (typeof j[l] == 'boolean') j[l] === true ? k[l] = true: k[l] = false;
                    }
                }
                return k;
            },
            getUrlParam: function(j, k) {
                var l = new RegExp('(?:[?&]|&amp;)' + j + '=([^&]+)', 'i'),
                m = (k || window).location.search.match(l);
                return m && m.length > 1 ? m[1] : null;
            },
            htmlEncode: function(j) {
                if (!j) return '';
                j = typeof j != 'string' ? j.toString() : j;
                j = j.replace(/&/g, '&amp;');
                j = j.replace(/</g, '&lt;');
                j = j.replace(/>/g, '&gt;');
                return j;
            },
            setCookie: function(j, k, l) {
                document.cookie = j + '=' + k + (!l ? '; expires=Thu, 6 Oct 2016 01:00:00 UTC; path=/': '');
            },
            getCookie: function(j) {
                var k = document.cookie.match(new RegExp('(^|\\s|;)' + j + '=([^;]*)'));
                return k && k.length > 0 ? k[2] : '';
            }
        };
        CKFinder._.callFunction = a.tools.callFunction;
        CKFinder.tools = a.tools;
    })();
    var i = a.tools;
    h.event = function(j) {
        this.$ = j;
    };
    h.event.prototype = {
        oV: function() {
            return this.$.keyCode || this.$.which;
        },
        db: function() {
            var k = this;
            var j = k.oV();
            if (k.$.ctrlKey || k.$.metaKey) j += a.bP;
            if (k.$.shiftKey) j += a.dy;
            if (k.$.altKey) j += a.eJ;
            return j;
        },
        preventDefault: function(j) {
            var k = this.$;
            if (k.preventDefault) k.preventDefault();
            else k.returnValue = false;
            if (j) this.stopPropagation();
        },
        stopPropagation: function() {
            var j = this.$;
            if (j.stopPropagation) j.stopPropagation();
            else j.cancelBubble = true;
        },
        bK: function() {
            var j = this.$.target || this.$.srcElement;
            return j ? new h.bi(j) : null;
        }
    };
    a.bP = 1000;
    a.dy = 2000;
    a.eJ = 4000;
    h.dE = function(j) {
        if (j) this.$ = j;
    };
    h.dE.prototype = (function() {
        var j = function(k, l) {
            return function(m) {
                if (typeof a != 'undefined') k.oW(l, new h.event(m));
            };
        };
        return {
            kk: function() {
                var k;
                if (! (k = this.dw('_'))) this.fL('_', k = {});
                return k;
            },
            on: function(k) {
                var n = this;
                var l = n.dw('_cke_nativeListeners');
                if (!l) {
                    l = {};
                    n.fL('_cke_nativeListeners', l);
                }
                if (!l[k]) {
                    var m = l[k] = j(n, k);
                    if (n.$.addEventListener && !g) n.$.addEventListener(k, m, !!a.event.jP);
                    else if (n.$.attachEvent) n.$.attachEvent('on' + k, m);
                }
                return a.event.prototype.on.apply(n, arguments);
            },
            aF: function(k) {
                var n = this;
                a.event.prototype.aF.apply(n, arguments);
                if (!n.rC(k)) {
                    var l = n.dw('_cke_nativeListeners'),
                    m = l && l[k];
                    if (m) {
                        if (n.$.removeEventListener) n.$.removeEventListener(k, m, false);
                        else if (n.$.detachEvent) n.$.detachEvent('on' + k, m);
                        delete l[k];
                    }
                }
            }
        };
    })();
    (function(j) {
        var k = {};
        j.equals = function(l) {
            return l && l.$ === this.$;
        };
        j.fL = function(l, m) {
            var n = this.iY(),
            o = k[n] || (k[n] = {});
            o[l] = m;
            return this;
        };
        j.dw = function(l) {
            var m = this.$.dj,
            n = m && k[m];
            return n && n[l];
        };
        j.jF = function(l) {
            var m = this.$.dj,
            n = m && k[m],
            o = n && n[l];
            if (typeof o != 'undefined') delete n[l];
            return o || null;
        };
        j.iY = function() {
            return this.$.dj || (this.$.dj = i.getNextNumber());
        };
        a.event.du(j);
    })(h.dE.prototype);
    h.window = function(j) {
        h.dE.call(this, j);
    };
    h.window.prototype = new h.dE();
    i.extend(h.window.prototype, {
        focus: function() {
            if (f.webkit && this.$.parent) this.$.parent.focus();
            this.$.focus();
        },
        eR: function() {
            var j = this.$.document,
            k = j.compatMode == 'CSS1Compat';
            return {
                width: (k ? j.documentElement.clientWidth: j.body.clientWidth) || 0,
                height: (k ? j.documentElement.clientHeight: j.body.clientHeight) || 0
            };
        },
        hV: function() {
            var j = this.$;
            if ('pageXOffset' in j) return {
                x: j.pageXOffset || 0,
                y: j.pageYOffset || 0
            };
            else {
                var k = j.document;
                return {
                    x: k.documentElement.scrollLeft || k.body.scrollLeft || 0,
                    y: k.documentElement.scrollTop || k.body.scrollTop || 0
                };
            }
        }
    });
    h.document = function(j) {
        h.dE.call(this, j);
    };
    var j = h.document;
    j.prototype = new h.dE();
    i.extend(j.prototype, {
        pb: function(k) {
            if (this.$.createStyleSheet) this.$.createStyleSheet(k);
            else {
                var l = new h.ax('link');
                l.setAttributes({
                    rel: 'stylesheet',
                    type: 'text/css',
                    href: k
                });
                this.eD().append(l);
            }
        },
        createElement: function(k, l) {
            var m = new h.ax(k, this);
            if (l) {
                if (l.attributes) m.setAttributes(l.attributes);
                if (l.gS) m.setStyles(l.gS);
            }
            return m;
        },
        jT: function(k) {
            return new h.text(k, this);
        },
        focus: function() {
            this.getWindow().focus();
        },
        getById: function(k) {
            var l = this.$.getElementById(k);
            return l ? new h.ax(l) : null;
        },
        vu: function(k, l) {
            var m = this.$.documentElement;
            for (var n = 0; m && n < k.length; n++) {
                var o = k[n];
                if (!l) {
                    m = m.childNodes[o];
                    continue;
                }
                var p = -1;
                for (var q = 0; q < m.childNodes.length; q++) {
                    var r = m.childNodes[q];
                    if (l === true && r.nodeType == 3 && r.previousSibling && r.previousSibling.nodeType == 3) continue;
                    p++;
                    if (p == o) {
                        m = r;
                        break;
                    }
                }
            }
            return m ? new h.bi(m) : null;
        },
        eG: function(k, l) {
            if (!g && l) k = l + ':' + k;
            return new h.iT(this.$.getElementsByTagName(k));
        },
        eD: function() {
            var k = this.$.getElementsByTagName('head')[0];
            k = new h.ax(k);
            return (this.eD = function() {
                return k;
            })();
        },
        bH: function() {
            var k = new h.ax(this.$.body);
            return (this.bH = function() {
                return k;
            })();
        },
        gT: function() {
            var k = new h.ax(this.$.documentElement);
            return (this.gT = function() {
                return k;
            })();
        },
        getWindow: function() {
            var k = new h.window(this.$.parentWindow || this.$.defaultView);
            return (this.getWindow = function() {
                return k;
            })();
        }
    });
    h.bi = function(k) {
        if (k) {
            switch (k.nodeType) {
            case a.cv:
                return new h.ax(k);
            case a.fl:
                return new h.text(k);
            }
            h.dE.call(this, k);
        }
        return this;
    };
    h.bi.prototype = new h.dE();
    a.cv = 1;
    a.fl = 3;
    a.va = 8;
    a.om = 11;
    a.oh = 0;
    a.op = 1;
    a.gW = 2;
    a.gX = 4;
    a.mo = 8;
    a.lF = 16;
    i.extend(h.bi.prototype, {
        appendTo: function(k, l) {
            k.append(this, l);
            return k;
        },
        clone: function(k, l) {
            var m = this.$.cloneNode(k);
            if (!l) {
                var n = function(o) {
                    if (o.nodeType != a.cv) return;
                    o.removeAttribute('id', false);
                    o.removeAttribute('dj', false);
                    var p = o.childNodes;
                    for (var q = 0; q < p.length; q++) n(p[q]);
                };
                n(m);
            }
            return new h.bi(m);
        },
        gE: function() {
            return ! !this.$.previousSibling;
        },
        ge: function() {
            return ! !this.$.nextSibling;
        },
        kB: function(k) {
            k.$.parentNode.insertBefore(this.$, k.$.nextSibling);
            return k;
        },
        insertBefore: function(k) {
            k.$.parentNode.insertBefore(this.$, k.$);
            return k;
        },
        vP: function(k) {
            this.$.parentNode.insertBefore(k.$, this.$);
            return k;
        },
        lU: function(k) {
            var l = [],
            m = this.getDocument().$.documentElement,
            n = this.$;
            while (n && n != m) {
                var o = n.parentNode,
                p = -1;
                for (var q = 0; q < o.childNodes.length; q++) {
                    var r = o.childNodes[q];
                    if (k && r.nodeType == 3 && r.previousSibling && r.previousSibling.nodeType == 3) continue;
                    p++;
                    if (r == n) break;
                }
                l.unshift(p);
                n = n.parentNode;
            }
            return l;
        },
        getDocument: function() {
            var k = new j(this.$.ownerDocument || this.$.parentNode.ownerDocument);
            return (this.getDocument = function() {
                return k;
            })();
        },
        vA: function() {
            var k = this.$,
            l = k.parentNode && k.parentNode.firstChild,
            m = -1;
            while (l) {
                m++;
                if (l == k) return m;
                l = l.nextSibling;
            }
            return - 1;
        },
        hL: function(k, l, m) {
            if (m && !m.call) {
                var n = m;
                m = function(q) {
                    return ! q.equals(n);
                };
            }
            var o = !k && this.getFirst && this.getFirst(),
            p;
            if (!o) {
                if (this.type == a.cv && m && m(this, true) === false) return null;
                o = this.dG();
            }
            while (!o && (p = (p || this).getParent())) {
                if (m && m(p, true) === false) return null;
                o = p.dG();
            }
            if (!o) return null;
            if (m && m(o) === false) return null;
            if (l && l != o.type) return o.hL(false, l, m);
            return o;
        },
        hZ: function(k, l, m) {
            if (m && !m.call) {
                var n = m;
                m = function(q) {
                    return ! q.equals(n);
                };
            }
            var o = !k && this.dB && this.dB(),
            p;
            if (!o) {
                if (this.type == a.cv && m && m(this, true) === false) return null;
                o = this.cf();
            }
            while (!o && (p = (p || this).getParent())) {
                if (m && m(p, true) === false) return null;
                o = p.cf();
            }
            if (!o) return null;
            if (m && m(o) === false) return null;
            if (l && o.type != l) return o.hZ(false, l, m);
            return o;
        },
        cf: function(k) {
            var l = this.$,
            m;
            do {
                l = l.previousSibling;
                m = l && new h.bi(l);
            } while ( m && k && ! k ( m ));
            return m;
        },
        vs: function() {
            return this.cf(function(k) {
                return k.$.nodeType == 1;
            });
        },
        dG: function(k) {
            var l = this.$,
            m;
            do {
                l = l.nextSibling;
                m = l && new h.bi(l);
            } while ( m && k && ! k ( m ));
            return m;
        },
        vk: function() {
            return this.dG(function(k) {
                return k.$.nodeType == 1;
            });
        },
        getParent: function() {
            var k = this.$.parentNode;
            return k && k.nodeType == 1 ? new h.bi(k) : null;
        },
        vn: function(k) {
            var l = this,
            m = [];
            do m[k ? 'push': 'unshift'](l);
            while (l = l.getParent());
            return m;
        },
        vv: function(k) {
            var m = this;
            if (k.equals(m)) return m;
            if (k.contains && k.contains(m)) return k;
            var l = m.contains ? m: m.getParent();
            do {
                if (l.contains(k)) return l;
            } while ( l = l . getParent ());
            return null;
        },
        gz: function(k) {
            var l = this.$,
            m = k.$;
            if (l.compareDocumentPosition) return l.compareDocumentPosition(m);
            if (l == m) return a.oh;
            if (this.type == a.cv && k.type == a.cv) {
                if (l.contains) {
                    if (l.contains(m)) return a.lF + a.gX;
                    if (m.contains(l)) return a.mo + a.gW;
                }
                if ('sourceIndex' in l) return l.sourceIndex < 0 || m.sourceIndex < 0 ? a.op: l.sourceIndex < m.sourceIndex ? a.gX: a.gW;
            }
            var n = this.lU(),
            o = k.lU(),
            p = Math.min(n.length, o.length);
            for (var q = 0; q <= p - 1; q++) {
                if (n[q] != o[q]) {
                    if (q < p) return n[q] < o[q] ? a.gX: a.gW;
                    break;
                }
            }
            return n.length < o.length ? a.lF + a.gX: a.mo + a.gW;
        },
        vw: function(k, l) {
            var m = this.$;
            if (!l) m = m.parentNode;
            while (m) {
                if (m.nodeName && m.nodeName.toLowerCase() == k) return new h.bi(m);
                m = m.parentNode;
            }
            return null;
        },
        vX: function(k, l) {
            var m = this.$;
            if (!l) m = m.parentNode;
            while (m) {
                if (m.nodeName && m.nodeName.toLowerCase() == k) return true;
                m = m.parentNode;
            }
            return false;
        },
        move: function(k, l) {
            k.append(this.remove(), l);
        },
        remove: function(k) {
            var l = this.$,
            m = l.parentNode;
            if (m) {
                if (k) for (var n; n = l.firstChild;) m.insertBefore(l.removeChild(n), l);
                m.removeChild(l);
            }
            return this;
        },
        replace: function(k) {
            this.insertBefore(k);
            k.remove();
        },
        trim: function() {
            this.ltrim();
            this.rtrim();
        },
        ltrim: function() {
            var n = this;
            var k;
            while (n.getFirst && (k = n.getFirst())) {
                if (k.type == a.fl) {
                    var l = i.ltrim(k.getText()),
                    m = k.hJ();
                    if (!l) {
                        k.remove();
                        continue;
                    } else if (l.length < m) {
                        k.split(m - l.length);
                        n.$.removeChild(n.$.firstChild);
                    }
                }
                break;
            }
        },
        rtrim: function() {
            var n = this;
            var k;
            while (n.dB && (k = n.dB())) {
                if (k.type == a.fl) {
                    var l = i.rtrim(k.getText()),
                    m = k.hJ();
                    if (!l) {
                        k.remove();
                        continue;
                    } else if (l.length < m) {
                        k.split(l.length);
                        n.$.lastChild.parentNode.removeChild(n.$.lastChild);
                    }
                }
                break;
            }
            if (!g && !f.opera) {
                k = n.$.lastChild;
                if (k && k.type == 1 && k.nodeName.toLowerCase() == 'br') k.parentNode.removeChild(k);
            }
        }
    });
    h.iT = function(k) {
        this.$ = k;
    };
    h.iT.prototype = {
        count: function() {
            return this.$.length;
        },
        getItem: function(k) {
            var l = this.$[k];
            return l ? new h.bi(l) : null;
        }
    };
    h.ax = function(k, l) {
        if (typeof k == 'string') k = (l ? l.$: document).createElement(k);
        h.dE.call(this, k);
    };
    var k = h.ax;
    k.eB = function(l) {
        return l && (l.$ ? l: new k(l));
    };
    k.prototype = new h.bi();
    k.et = function(l, m) {
        var n = new k('div', m);
        n.setHtml(l);
        return n.getFirst().remove();
    };
    k.rS = function(l, m, n, o) {
        var p = m.dw('list_marker_id') || m.fL('list_marker_id', i.getNextNumber()).dw('list_marker_id'),
        q = m.dw('list_marker_names') || m.fL('list_marker_names', {}).dw('list_marker_names');
        l[p] = m;
        q[n] = 1;
        return m.fL(n, o);
    };
    k.sM = function(l) {
        for (var m in l) k.qZ(l, l[m], true);
    };
    k.qZ = function(l, m, n) {
        var o = m.dw('list_marker_names'),
        p = m.dw('list_marker_id');
        for (var q in o) m.jF(q);
        m.jF('list_marker_names');
        if (n) {
            m.jF('list_marker_id');
            delete l[p];
        }
    };
    i.extend(k.prototype, {
        type: a.cv,
        addClass: function(l) {
            var m = this.$.className;
            if (m) {
                var n = new RegExp('(?:^|\\s)' + l + '(?:\\s|$)', '');
                if (!n.test(m)) m += ' ' + l;
            }
            this.$.className = m || l;
        },
        removeClass: function(l) {
            var m = this.getAttribute('class');
            if (m) {
                var n = new RegExp('(?:^|\\s+)' + l + '(?=\\s|$)', 'i');
                if (n.test(m)) {
                    m = m.replace(n, '').replace(/^\s+/, '');
                    if (m) this.setAttribute('class', m);
                    else this.removeAttribute('class');
                }
            }
        },
        hasClass: function(l) {
            var m = new RegExp('(?:^|\\s+)' + l + '(?=\\s|$)', '');
            return m.test(this.getAttribute('class'));
        },
        append: function(l, m) {
            var n = this;
            if (typeof l == 'string') l = n.getDocument().createElement(l);
            if (m) n.$.insertBefore(l.$, n.$.firstChild);
            else n.$.appendChild(l.$);
            a.log('[DOM] DOM flush into ' + n.getName());
            return l;
        },
        appendHtml: function(l) {
            var n = this;
            if (!n.$.childNodes.length) n.setHtml(l);
            else {
                var m = new k('div', n.getDocument());
                m.setHtml(l);
                m.jg(n);
            }
        },
        appendText: function(l) {
            if (this.$.text != undefined) this.$.text += l;
            else this.append(new h.text(l));
        },
        pd: function() {
            var m = this;
            var l = m.dB();
            while (l && l.type == a.fl && !i.rtrim(l.getText())) l = l.cf();
            if (!l || !l.is || !l.is('br')) m.append(f.opera ? m.getDocument().jT('') : m.getDocument().createElement('br'));
        },
        tV: function(l) {
            var o = this;
            var m = new h.mk(o.getDocument());
            m.setStartAfter(o);
            m.setEndAfter(l);
            var n = m.extractContents();
            m.insertNode(o.remove());
            n.kA(o);
        },
        contains: g || f.webkit ?
        function(l) {
            var m = this.$;
            return l.type != a.cv ? m.contains(l.getParent().$) : m != l.$ && m.contains(l.$);
        }: function(l) {
            return ! ! (this.$.compareDocumentPosition(l.$) & 16);
        },
        focus: function() {
            try {
                this.$.focus();
            } catch(l) {}
        },
        getHtml: function() {
            return this.$.innerHTML;
        },
        vi: function() {
            var m = this;
            if (m.$.outerHTML) return m.$.outerHTML.replace(/<\?[^>]*>/, '');
            var l = m.$.ownerDocument.createElement('div');
            l.appendChild(m.$.cloneNode(true));
            return l.innerHTML;
        },
        setHtml: function(l) {
            a.log('[DOM] DOM flush into ' + this.getName());
            return this.$.innerHTML = l;
        },
        setText: function(l) {
            k.prototype.setText = this.$.innerText != undefined ?
            function(m) {
                a.log('[DOM] Text flush');
                return this.$.innerText = m;
            }: function(m) {
                a.log('[DOM] Text flush');
                return this.$.textContent = m;
            };
            return this.setText(l);
        },
        getAttribute: (function() {
            var l = function(m) {
                return this.$.getAttribute(m, 2);
            };
            if (g && (f.ie7Compat || f.ie6Compat)) return function(m) {
                var o = this;
                switch (m) {
                case 'class':
                    m = 'className';
                    break;
                case 'tabindex':
                    var n = l.call(o, m);
                    if (n !== 0 && o.$.tabIndex === 0) n = null;
                    return n;
                    break;
                case 'checked':
                    return o.$.checked;
                    break;
                case 'style':
                    return o.$.style.cssText;
                }
                return l.call(o, m);
            };
            else return l;
        })(),
        getChildren: function() {
            return new h.iT(this.$.childNodes);
        },
        getComputedStyle: g ?
        function(l) {
            return this.$.currentStyle[i.cssStyleToDomStyle(l)];
        }: function(l) {
            return this.getWindow().$.getComputedStyle(this.$, '').getPropertyValue(l);
        },
        pf: function() {
            var l = a.ga[this.getName()];
            this.pf = function() {
                return l;
            };
            return l;
        },
        eG: j.prototype.eG,
        vp: g ?
        function() {
            var l = this.$.tabIndex;
            if (l === 0 && !a.ga.ug[this.getName()] && parseInt(this.getAttribute('tabindex'), 10) !== 0) l = -1;
            return l;
        }: f.webkit ?
        function() {
            var l = this.$.tabIndex;
            if (l == undefined) {
                l = parseInt(this.getAttribute('tabindex'), 10);
                if (isNaN(l)) l = -1;
            }
            return l;
        }: function() {
            return this.$.tabIndex;
        },
        getText: function() {
            return this.$.textContent || this.$.innerText || '';
        },
        getWindow: function() {
            return this.getDocument().getWindow();
        },
        dS: function() {
            return this.$.id || null;
        },
        vm: function() {
            return this.$.name || null;
        },
        getName: function() {
            var l = this.$.nodeName.toLowerCase();
            if (g) {
                var m = this.$.scopeName;
                if (m != 'HTML') l = m.toLowerCase() + ':' + l;
            }
            return (this.getName = function() {
                return l;
            })();
        },
        getValue: function() {
            return this.$.value;
        },

        getFirst: function() {
            var l = this.$.firstChild;
            return l ? new h.bi(l) : null;
        },
        dB: function(l) {
            var m = this.$.lastChild,
            n = m && new h.bi(m);
            if (n && l && !l(n)) n = n.cf(l);
            return n;
        },
        rd: function(l) {
            return this.$.style[i.cssStyleToDomStyle(l)];
        },
        is: function() {
            var l = this.getName();
            for (var m = 0; m < arguments.length; m++) {
                if (arguments[m] == l) return true;
            }
            return false;
        },
        vL: function() {
            var l = this.getName(),
            m = !a.ga.uj[l] && (a.ga[l] || a.ga.span);
            return m && m['#'];
        },
        isIdentical: function(l) {
            if (this.getName() != l.getName()) return false;
            var m = this.$.attributes,
            n = l.$.attributes,
            o = m.length,
            p = n.length;
            if (!g && o != p) return false;
            for (var q = 0; q < o; q++) {
                var r = m[q];
                if ((!g || r.specified && r.nodeName != 'dj') && r.nodeValue != l.getAttribute(r.nodeName)) return false;
            }
            if (g) for (q = 0; q < p; q++) {
                r = n[q];
                if ((!g || r.specified && r.nodeName != 'dj') && r.nodeValue != m.getAttribute(r.nodeName)) return false;
            }
            return true;
        },
        isVisible: function() {
            return this.$.offsetWidth && this.$.style.visibility != 'hidden';
        },
        hasAttributes: g && (f.ie7Compat || f.ie6Compat) ?
        function() {
            var l = this.$.attributes;
            for (var m = 0; m < l.length; m++) {
                var n = l[m];
                switch (n.nodeName) {
                case 'class':
                    if (this.getAttribute('class')) return true;
                case 'dj':
                    continue;
                default:
                    if (n.specified) return true;
                }
            }
            return false;
        }:
        function() {
            var l = this.$.attributes;
            return l.length > 1 || l.length == 1 && l[0].nodeName != 'dj';
        },
        hasAttribute: function(l) {
            var m = this.$.attributes.getNamedItem(l);
            return ! ! (m && m.specified);
        },
        hide: function() {
            this.setStyle('display', 'none');
        },
        jg: function(l, m) {
            var n = this.$;
            l = l.$;
            if (n == l) return;
            var o;
            if (m) while (o = n.lastChild) l.insertBefore(n.removeChild(o), l.firstChild);
            else while (o = n.firstChild) l.appendChild(n.removeChild(o));
        },
        show: function() {
            this.setStyles({
                display: '',
                visibility: ''
            });
        },
        setAttribute: (function() {
            var l = function(m, n) {
                this.$.setAttribute(m, n);
                return this;
            };
            if (g && (f.ie7Compat || f.ie6Compat)) return function(m, n) {
                var o = this;
                if (m == 'class') o.$.className = n;
                else if (m == 'style') o.$.style.cssText = n;
                else if (m == 'tabindex') o.$.tabIndex = n;
                else if (m == 'checked') o.$.checked = n;
                else l.apply(o, arguments);
                return o;
            };
            else return l;
        })(),
        setAttributes: function(l) {
            for (var m in l) this.setAttribute(m, l[m]);
            return this;
        },
        setValue: function(l) {
            this.$.value = l;
            return this;
        },
        removeAttribute: (function() {
            var l = function(m) {
                this.$.removeAttribute(m);
            };
            if (g && (f.ie7Compat || f.ie6Compat)) return function(m) {
                if (m == 'class') m = 'className';
                else if (m == 'tabindex') m = 'tabIndex';
                l.call(this, m);
            };
            else return l;
        })(),
        uW: function(l) {
            for (var m = 0; m < l.length; m++) this.removeAttribute(l[m]);
        },
        removeStyle: function(l) {
            var m = this;
            if (m.$.style.removeAttribute) m.$.style.removeAttribute(i.cssStyleToDomStyle(l));
            else m.setStyle(l, '');
            if (!m.$.style.cssText) m.removeAttribute('style');
        },
        setStyle: function(l, m) {
            this.$.style[i.cssStyleToDomStyle(l)] = m;
            return this;
        },
        setStyles: function(l) {
            for (var m in l) this.setStyle(m, l[m]);
            return this;
        },
        setOpacity: function(l) {
            if (g) {
                l = Math.round(l * 100);
                this.setStyle('filter', l >= 100 ? '': 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + l + ')');
            } else this.setStyle('opacity', l);
        },
        unselectable: f.gecko ?
        function() {
            this.$.style.MozUserSelect = 'none';
        }: f.webkit ?
        function() {
            this.$.style.uE = 'none';
        }: function() {
            if (g || f.opera) {
                var l = this.$,
                m, n = 0;
                l.unselectable = 'on';
                while (m = l.all[n++]) switch (m.tagName.toLowerCase()) {
                case 'iframe':
                case 'textarea':
                case 'input':
                case 'select':
                    break;
                default:
                    m.unselectable = 'on';
                }
            }
        },
        vr: function() {
            var l = this;
            while (l.getName() != 'html') {
                if (l.getComputedStyle('position') != 'static') return l;
                l = l.getParent();
            }
            return null;
        },
        ir: function(l) {
            var G = this;
            var m = 0,
            n = 0,
            o = G.getDocument().bH(),
            p = G.getDocument().$.compatMode == 'BackCompat',
            q = G.getDocument();
            if (document.documentElement.getBoundingClientRect) {
                var r = G.$.getBoundingClientRect(),
                s = q.$,
                t = s.documentElement,
                u = t.clientTop || o.$.clientTop || 0,
                v = t.clientLeft || o.$.clientLeft || 0,
                w = true;
                if (g) {
                    var x = q.gT().contains(G),
                    y = q.bH().contains(G);
                    w = p && y || !p && x;
                }
                if (w) {
                    m = r.left + (!p && t.scrollLeft || o.$.scrollLeft);
                    m -= v;
                    n = r.top + (!p && t.scrollTop || o.$.scrollTop);
                    n -= u;
                }
            } else {
                var z = G,
                A = null,
                B;
                while (z && !(z.getName() == 'body' || z.getName() == 'html')) {
                    m += z.$.offsetLeft - z.$.scrollLeft;
                    n += z.$.offsetTop - z.$.scrollTop;
                    if (!z.equals(G)) {
                        m += z.$.clientLeft || 0;
                        n += z.$.clientTop || 0;
                    }
                    var C = A;
                    while (C && !C.equals(z)) {
                        m -= C.$.scrollLeft;
                        n -= C.$.scrollTop;
                        C = C.getParent();
                    }
                    A = z;
                    z = (B = z.$.offsetParent) ? new k(B) : null;
                }
            }
            if (l) {
                var D = G.getWindow(),
                E = l.getWindow();
                if (!D.equals(E) && D.$.frameElement) {
                    var F = new k(D.$.frameElement).ir(l);
                    m += F.x;
                    n += F.y;
                }
            }
            if (!document.documentElement.getBoundingClientRect) if (f.gecko && !p) {
                m += G.$.clientLeft ? 1 : 0;
                n += G.$.clientTop ? 1 : 0;
            }
            return {
                x: m,
                y: n
            };
        },
        scrollIntoView: function(l) {
            var r = this;
            var m = r.getWindow(),
            n = m.eR().height,
            o = n * -1;
            if (l) o += n;
            else {
                o += r.$.offsetHeight || 0;
                o += parseInt(r.getComputedStyle('marginBottom') || 0, 10) || 0;
            }
            var p = r.ir();
            o += p.y;
            o = o < 0 ? 0 : o;
            var q = m.hV().y;
            if (o > q || o < q - n) m.$.scrollTo(0, o);
        },
        bR: function(l) {
            var m = this;
            switch (l) {
            case a.eV:
                m.addClass('cke_on');
                m.removeClass('cke_off');
                m.removeClass('cke_disabled');
                break;
            case a.aY:
                m.addClass('cke_disabled');
                m.removeClass('cke_off');
                m.removeClass('cke_on');
                break;
            default:
                m.addClass('cke_off');
                m.removeClass('cke_on');
                m.removeClass('cke_disabled');
                break;
            }
        },
        getFrameDocument: function() {
            var l = this.$;
            try {
                l.contentWindow.document;
            } catch(m) {
                l.src = l.src;
                if (g && f.version < 7) window.showModalDialog('javascript:document.write("<script>window.setTimeout(function(){window.close();},50);</script>")');
            }
            return l && new j(l.contentWindow.document);
        },
        qw: function(l, m) {
            var s = this;
            var n = s.$.attributes;
            m = m || {};
            for (var o = 0; o < n.length; o++) {
                var p = n[o];
                if (p.specified || g && p.nodeValue && p.nodeName.toLowerCase() == 'value') {
                    var q = p.nodeName;
                    if (q in m) continue;
                    var r = s.getAttribute(q);
                    if (r === null) r = p.nodeValue;
                    l.setAttribute(q, r);
                }
            }
            if (s.$.style.cssText !== '') l.$.style.cssText = s.$.style.cssText;
        },
        renameNode: function(l) {
            var o = this;
            if (o.getName() == l) return;
            var m = o.getDocument(),
            n = new k(l, m);
            o.qw(n);
            o.jg(n);
            o.$.parentNode.replaceChild(n.$, o.$);
            n.$.dj = o.$.dj;
            o.$ = n.$;
        },
        aC: function(l) {
            var m = this.$;
            if (!l.slice) m = m.childNodes[l];
            else while (l.length > 0 && m) m = m.childNodes[l.shift()];
            return m ? new h.bi(m) : null;
        },
        iu: function() {
            return this.$.childNodes.length;
        },
        hX: function() {
            this.on('contextmenu',
            function(l) {
                if (!l.data.bK().hasClass('cke_enable_context_menu')) l.data.preventDefault();
            });
        },
        'toString': function() {
            return this.getName() + '#' + this.dS() + '.' + this.getAttribute('class');
        }
    });
    a.command = function(l, m) {
        this.pW = [];
        this.exec = function(n) {
            if (this.bu == a.aY) return false;
            if (m.oD) l.focus();
            return m.exec.call(this, l, n) !== false;
        };
        i.extend(this, m, {
            iH: {
                qt: 1
            },
            oD: true,
            bu: a.aS
        });
        a.event.call(this);
    };
    a.command.prototype = {
        enable: function() {
            var l = this;
            if (l.bu == a.aY) l.bR(!l.vf || typeof l.lJ == 'undefined' ? a.aS: l.lJ);
        },
        disable: function() {
            this.bR(a.aY);
        },
        bR: function(l) {
            var m = this;
            if (m.bu == l) return false;
            m.lJ = m.bu;
            m.bu = l;
            m.oW('bu');
            return true;
        },
        rJ: function() {
            var l = this;
            if (l.bu == a.aS) l.bR(a.eV);
            else if (l.bu == a.eV) l.bR(a.aS);
        }
    };
    a.event.du(a.command.prototype, true);
    a.config = {
        customConfig: a.getUrl('config.js'),
        connectorLanguage: 'php',
        language: '',
        defaultLanguage: 'en',
        defaultViewType: 'thumbnails',
        defaultSortBy: 'filename',
        defaultDisplayFilename: true,
        defaultDisplayDate: true,
        defaultDisplayFilesize: true,
        pO: '',
        height: 400,
        plugins: 'foldertree,folder,filebrowser,container,connector,resource,toolbar,formpanel,filesview,status,contextmenu,uploadform,keystrokes,dragdrop,basket,dialog,tools,resize,help',
        extraPlugins: '',
        fileIcons: 'ai|avi|bmp|cs|dll|doc|docx|exe|fla|gif|jpg|js|mdb|mp3|ogg|pdf|ppt|pptx|rdp|swf|swt|txt|vsd|xls|xlsx|xml|zip',
        removePlugins: '',
        tabIndex: 0,
        theme: 'default',
        skin: 'kama',
        width: '100%',
        baseFloatZIndex: 10000,
        directDownload: false,
        log: false,
        logStackTrace: false,
        rememberLastFolder: true,
        id: null,
        startupPath: '',
        startupFolderExpanded: true,
        selectActionFunction: null,
        selectActionData: null,
        selectThumbnailActionFunction: null,
        selectThumbnailActionData: null,
        disableThumbnailSelection: false,
        thumbsUrl: null,
        thumbsDirectAccess: false,
        imagesMaxWidth: 0,
        imagesMaxHeight: 0,
        selectActionType: 'js',
        resourceType: null,
        disableHelpButton: false,
        connectorPath: 'core/connector/'
    };
    CKFinder.config = a.config;
    var l = a.config;
    a.dU = function(m, n) {
        this.rG = m;
        this.message = n;
    };
    a.fs = function(m) {
        if (m.fs) return m.fs;
        this.hasFocus = false;
        this._ = {
            application: m
        };
        return this;
    };
    a.fs.prototype = {
        focus: function() {
            var n = this;
            if (n._.fW) clearTimeout(n._.fW);
            if (!n.hasFocus) {
                if (a.eq) a.eq.fs.ly();
                var m = n._.application;
                m.container.getFirst().addClass('cke_focus');
                n.hasFocus = true;
                m.oW('focus');
            }
        },
        blur: function() {
            var m = this;
            if (m._.fW) clearTimeout(m._.fW);
            m._.fW = setTimeout(function() {
                delete m._.fW;
                m.ly();
            },
            100);
        },
        ly: function() {
            if (this.hasFocus) {
                var m = this._.application;
                m.container.getFirst().removeClass('cke_focus');
                this.hasFocus = false;
                m.oW('blur');
            }
        }
    };
    (function() {
        var m = {};
        a.lang = {
            ko: {
                cs: 1,
                da: 1,
                de: 1,
                el: 1,
                en: 1,
                es: 1,
                'es-mx': 1,
                fi: 1,
                fr: 1,
                he: 1,
                hu: 1,
                it: 1,
                ja: 1,
                lv: 1,
                nb: 1,
                nl: 1,
                no: 1,
                nn: 1,
                pl: 1,
                'pt-br': 1,
                ru: 1,
                sk: 1,
                sl: 1,
                sv: 1,
                'zh-cn': 1,
                'zh-tw': 1
            },
            load: function(n, o, p) {
                if (!n || !a.lang.ko[n]) n = this.jV(o, n);
                if (!this[n]) a.ec.load(a.getUrl('lang/' + n + '.js'),
                function() {
                    p(n, CKFinder.lang[n]);
                },
                this);
                else p(n, this[n]);
            },
            jV: function(n, o) {
                var p = this.ko;
                o = o || navigator.userLanguage || navigator.language;
                var q = o.toLowerCase().match(/([a-z]+)(?:-([a-z]+))?/),
                r = q[1],
                s = q[2];
                if (p[r + '-' + s]) r = r + '-' + s;
                else if (!p[r]) r = null;
                a.lang.jV = r ?
                function() {
                    return r;
                }: function(t) {
                    return t;
                };
                return r || n;
            }
        };
    })();
    (function() {
        a.log = function() {
            if (!l.log && !window.CKFINDER_LOG) return;
            var m = '';
            for (var n = 0; n < arguments.length; n++) {
                var o = arguments[n];
                if (!o) continue;
                if (m) m += '; ';
                switch (typeof o) {
                case 'function':
                    var p = /function (\w+?)\(/.exec(o.toString());
                    p = p ? p[1] : 'anonymous func';
                    m += p;
                    break;
                default:
                    m += o ? o.toString() : '';
                }
            }
            a._.log.push(m);
            if (typeof window.console == 'object') if (!console.log.apply) console.log(m);
            else console.log.apply(console, arguments);
        };
        a.ba = function(m) {
            if (l.logStackTrace) a.log('[EXCEPTION] ' + m.toString());
            return m;
        };
        a.mZ = function(m) {
            var n = '';
            for (var o = 0; o < a._.log.length; o++) n += o + 1 + '. ' + a._.log[o] + '\n';
            return n;
        };
        a._.log = [];
    })();
    a.ec = (function() {
        var m = {},
        n = {};
        return {
            load: function(o, p, q, r, s) {
                var t = typeof o == 'string';
                if (t) o = [o];
                if (!q) q = a;
                var u = o.length,
                v = [],
                w = [],
                x = function(C) {
                    if (p) if (t) p.call(q, C);
                    else p.call(q, v, w);
                };
                if (u === 0) {
                    x(true);
                    return;
                }
                var y = function(C, D) { (D ? v: w).push(C);
                    if (--u <= 0) x(D);
                },
                z = function(C, D) {
                    m[C] = 1;
                    var E = n[C];
                    delete n[C];
                    for (var F = 0; F < E.length; F++) E[F](C, D);
                },
                A = function(C) {
                    if (r !== true && m[C]) {
                        y(C, true);
                        return;
                    }
                    var D = n[C] || (n[C] = []);
                    D.push(y);
                    if (D.length > 1) return;
                    var E = new k('script');
                    E.setAttributes({
                        type: 'text/javascript',
                        src: C
                    });
                    if (p) if (g) E.$.onreadystatechange = function() {
                        if (E.$.readyState == 'loaded' || E.$.readyState == 'complete') {
                            E.$.onreadystatechange = null;
                            a.log('[LOADED] ' + C);
                            z(C, true);
                        }
                    };
                    else {
                        E.$.onload = function() {
                            setTimeout(function() {
                                a.log('[LOADED] ' + C);
                                z(C, true);
                            },
                            0);
                        };
                        E.$.onerror = function() {
                            z(C, false);
                        };
                    }
                    E.appendTo(s ? s: a.document.eD());
                };
                for (var B = 0; B < u; B++) A(o[B]);
            },
            uq: function(o) {
                var p = new k('script');
                p.setAttribute('type', 'text/javascript');
                p.appendText(o);
                p.appendTo(a.document.eD());
            }
        };
    })();
    a.fQ = function(m, n) {
        var o = this;
        o.basePath = m;
        o.fileName = n;
        o.bX = {};
        o.loaded = {};
        o.jn = {};
        o._ = {
            rZ: {}
        };
    };
    a.fQ.prototype = {
        add: function(m, n) {
            if (this.bX[m]) throw '[CKFINDER.fQ.add] The resource name "' + m + '" is already bX.';
            this.bX[m] = n || {};
        },
        eB: function(m) {
            return this.bX[m] || null;
        },
        getPath: function(m) {
            var n = this.jn[m];
            return a.getUrl(n && n.dir || this.basePath + m + '/');
        },
        pi: function(m) {
            var n = this.jn[m];
            return a.getUrl(this.getPath(m) + (n && n.file || this.fileName + '.js'));
        },
        tR: function(m, n, o) {
            m = m.split(',');
            for (var p = 0; p < m.length; p++) {
                var q = m[p];
                this.jn[q] = {
                    dir: n,
                    file: o
                };
            }
        },
        load: function(m, n, o) {
            if (!i.isArray(m)) m = m ? [m] : [];
            var p = this.loaded,
            q = this.bX,
            r = [],
            s = {},
            t = {};
            for (var u = 0; u < m.length; u++) {
                var v = m[u];
                if (!v) continue;
                if (!p[v] && !q[v]) {
                    var w = this.pi(v);
                    r.push(w);
                    if (! (w in s)) s[w] = [];
                    s[w].push(v);
                } else t[v] = this.eB(v);
            }
            a.ec.load(r,
            function(x, y) {
                if (y.length) throw '[CKFINDER.fQ.load] Resource name "' + s[y[0]].join(',') + '" was not found at "' + y[0] + '".';
                for (var z = 0; z < x.length; z++) {
                    var A = s[x[z]];
                    for (var B = 0; B < A.length; B++) {
                        var C = A[B];
                        t[C] = this.eB(C);
                        p[C] = 1;
                    }
                }
                n.call(o, t);
            },
            this);
        }
    };
    a.plugins = new a.fQ('plugins/', 'plugin');
    var m = a.plugins;
    m.load = i.override(m.load,
    function(n) {
        return function(o, p, q) {
            var r = {},
            s = function(t) {
                n.call(this, t,
                function(u) {
                    i.extend(r, u);
                    var v = [];
                    for (var w in u) {
                        var x = u[w],
                        y = x && x.bM;
                        if (y) for (var z = 0; z < y.length; z++) {
                            if (!r[y[z]]) v.push(y[z]);
                        }
                    }
                    if (v.length) s.call(this, v);
                    else {
                        for (w in r) {
                            x = r[w];
                            if (x.onLoad && !x.onLoad.qK) {
                                x.onLoad();
                                x.onLoad.qK = 1;
                            }
                        }
                        if (p) p.call(q || window, r);
                    }
                },
                this);
            };
            s.call(this, o);
        };
    });
    m.rX = function(n, o, p) {
        var q = this.eB(n);
        q.lang[o] = p;
    };
    (function() {
        var n = {},
        o = function(p, q) {
            var r = function() {
                n[p] = 1;
                q();
            },
            s = new k('img');
            s.on('load', r);
            s.on('error', r);
            s.setAttribute('src', p);
        };
        a.rw = {
            load: function(p, q) {
                var r = p.length,
                s = function() {
                    if (--r === 0) q();
                };
                for (var t = 0; t < p.length; t++) {
                    var u = p[t];
                    if (n[u]) s();
                    else o(u, s);
                }
            }
        };
    })();
    a.skins = (function() {
        var n = {},
        o = {},
        p = {},
        q = function(r, s, t, u) {
            var v = n[s];
            if (!r.skin) {
                r.skin = v;
                if (v.bz) v.bz(r);
            }
            var w = function(E) {
                for (var F = 0; F < E.length; F++) E[F] = a.getUrl(p[s] + E[F]);
            };
            if (!o[s]) {
                var x = v.ls;
                if (x && x.length > 0) {
                    w(x);
                    a.rw.load(x,
                    function() {
                        o[s] = 1;
                        q(r, s, t, u);
                    });
                    return;
                }
                o[s] = 1;
            }
            t = v[t];
            var y = 0;
            if (t) {
                if (!t.iB) t.iB = [];
                else if (t.iB[r.name]) y = 1;
            } else y = 1;
            if (y) u && u();
            else {
                if (t.eb === undefined) t.eb = [];
                if (t.eb[r.name] === undefined) t.eb[r.name] = [];
                var z = t.eb[r.name];
                z.push(u);
                if (z.length > 1) return;
                var A = !t.css || !t.css.length,
                B = !t.js || !t.js.length,
                C = function() {
                    if (A && B) {
                        t.iB[r.name] = 1;
                        for (var E = 0; E < z.length; E++) {
                            if (z[E]) z[E]();
                        }
                    }
                };
                if (!A) {
                    if (!t.rr) {
                        w(t.css);
                        t.rr = 1;
                    }
                    if (t.qx) for (var D = 0; D < t.css.length; D++) a.oC.pb(t.css[D]);
                    else r.on('themeSpace',
                    function(E) {
                        if (E.data.space == 'head') for (var F = 0; F < t.css.length; F++) E.data.html += "<link rel='stylesheet' href='" + t.css[F] + "'>\n";
                        E.aF();
                    });
                    A = 1;
                }
                if (!B) {
                    w(t.js);
                    r.ec.load(t.js,
                    function() {
                        B = 1;
                        C();
                    });
                }
                C();
            }
        };
        return {
            add: function(r, s) {
                n[r] = s;
                s.fh = p[r] || (p[r] = a.getUrl('skins/' + r + '/'));
            },
            loaded: n,
            load: function(r, s, t) {
                var u = r.gd,
                v = r.fh;
                if (n[u]) {
                    q(r, u, s, t);
                    var w = n[u];
                } else {
                    p[u] = v;
                    a.ec.load(v + 'skin.js',
                    function() {
                        q(r, u, s, t);
                    });
                }
            }
        };
    })();
    a.gc = new a.fQ('gc/', 'theme');
    a.bY = function(n) {
        if (n.bY) return n.bY;
        this._ = {
            jZ: {},
            items: {},
            application: n
        };
        return this;
    };
    var n = a.bY;
    n.prototype = {
        add: function(o, p, q) {
            this._.items[o] = {
                type: p,
                command: q.command || null,
                mp: Array.prototype.slice.call(arguments, 2)
            };
        },
        create: function(o) {
            var t = this;
            var p = t._.items[o],
            q = p && t._.jZ[p.type],
            r = p && p.command && t._.application.cS(p.command),
            s = q && q.create.apply(t, p.mp);
            if (r) r.pW.push(s);
            return s;
        },
        kd: function(o, p) {
            this._.jZ[o] = p;
        }
    };
    (function() {
        var o = 0,
        p = function() {
            var y = 'ckfinder' + ++o;
            return a.instances && a.instances[y] ? p() : y;
        },
        q = {},
        r = function(y) {
            var z = y.config.customConfig;
            if (!z) return false;
            var A = q[z] || (q[z] = {});
            if (A.gg) {
                A.gg.call(y, y.config);
                if (y.config.customConfig == z || !r(y)) y.cr('customConfigLoaded');
            } else a.ec.load(z,
            function() {
                if (CKFinder.customConfig) A.gg = CKFinder.customConfig;
                else A.gg = function() {};
                r(y);
            });
            return true;
        },
        s = function(y, z) {
            y.on('customConfigLoaded',
            function() {
                if (z) {
                    if (z.on) for (var A in z.on) y.on(A, z.on[A]);
                    i.extend(y.config, z, true);
                    delete y.config.on;
                }
                t(y);
            });
            if (z && z.customConfig != undefined) y.config.customConfig = z.customConfig;
            if (!r(y)) y.cr('customConfigLoaded');
        },
        t = function(y) {
            var z = y.config.skin.split(','),
            A = z[0],
            B = a.getUrl(z[1] || 'skins/' + A + '/');
            y.gd = A;
            y.fh = B;
            y.iy = 'cke_skin_' + A + ' skin_' + A;
            y.qn = y.ox();
            y.on('uiReady',
            function() {
                y.document.getWindow().on('lW',
                function() {
                    i.setCookie('CKFinder_UTime', Math.round(new Date().getTime() / 1000), true);
                    i.setCookie('CKFinder_UId', encodeURIComponent(y.id ? y.id: location.href), true);
                });
            });
            y.cr('configLoaded');
            u(y);
        },
        u = function(y) {
            a.lang.load(y.config.language, y.config.defaultLanguage,
            function(z, A) {
                y.langCode = z;
                y.lang = i.prototypedCopy(A);
                y.lB = (function() {
                    var B = "['" + y.lang.DateAmPm.join("','") + "']",
                    C = y.lang.DateTime.replace(/dd|mm|yyyy|hh|HH|MM|aa|d|m|yy|h|H|M|a/g,
                    function(D) {
                        var E;
                        switch (D) {
                        case 'd':
                            E = "day.replace(/^0/,'')";
                            break;
                        case 'dd':
                            E = 'day';
                            break;
                        case 'm':
                            E = "month.replace(/^0/,'')";
                            break;
                        case 'mm':
                            E = 'month';
                            break;
                        case 'yy':
                            E = 'year.substr(2)';
                            break;
                        case 'yyyy':
                            E = 'year';
                            break;
                        case 'H':
                            E = "hour.replace(/^0/,'')";
                            break;
                        case 'HH':
                            E = 'hour';
                            break;
                        case 'h':
                            E = "( hour < 12 ? hour : ( ( hour - 12 ) + 100 ).toString().substr( 1 ) ).replace(/^0/,'')";
                            break;
                        case 'hh':
                            E = '( hour < 12 ? hour : ( ( hour - 12 ) + 100 ).toString().substr( 1 ) )';
                            break;
                        case 'M':
                            E = "minute.replace(/^0/,'')";
                            break;
                        case 'MM':
                            E = 'minute';
                            break;
                        case 'a':
                            E = B + '[ hour < 12 ? 0 : 1 ].charAt(0)';
                            break;
                        case 'aa':
                            E = B + '[ hour < 12 ? 0 : 1 ]';
                            break;
                        default:
                            E = "'" + D + "'";
                        }
                        return "'," + E + ",'";
                    });
                    C = "'" + C + "'";
                    C = C.replace(/('',)|,''$/g, '');
                    return new Function('day', 'month', 'year', 'hour', 'minute', 'return [' + C + "].join('');");
                })();
                if (f.gecko && f.version < 10900 && y.lang.dir == 'rtl') y.lang.dir = 'ltr';
                v(y);
            });
        },
        v = function(y) {
            var z = y.config,
            A = z.plugins,
            B = z.extraPlugins,
            C = z.removePlugins;
            if (B) {
                var D = new RegExp('(?:^|,)(?:' + B.replace(/\s*,\s*/g, '|') + ')(?=,|$)', 'g');
                A = A.replace(D, '');
                A += ',' + B;
            }
            if (C) {
                D = new RegExp('(?:^|,)(?:' + C.replace(/\s*,\s*/g, '|') + ')(?=,|$)', 'g');
                A = A.replace(D, '');
            }
            m.load(A.split(','),
            function(E) {
                var F = [],
                G = [],
                H = [];
                y.plugins = E;
                for (var I in E) {
                    var J = E[I],
                    K = J.lang,
                    L = m.getPath(I),
                    M = null;
                    E[I].name = I;
                    J.pathName = L;
                    if (K) {
                        M = i.indexOf(K, y.langCode) >= 0 ? y.langCode: K[0];
                        if (!J.lang[M]) H.push(a.getUrl(L + 'lang/' + M + '.js'));
                        else {
                            i.extend(y.lang, J.lang[M]);
                            M = null;
                        }
                    }
                    G.push(M);
                    F.push(J);
                }
                a.ec.load(H,
                function() {
                    var N = ['eK', 'bz', 'gr'];
                    for (var O = 0; O < N.length; O++) for (var P = 0; P < F.length; P++) {
                        var Q = F[P];
                        if (O === 0 && G[P] && Q.lang) i.extend(y.lang, Q.lang[G[P]]);
                        if (Q[N[O]]) {
                            a.log('[PLUGIN] ' + Q.name + '.' + N[O]);
                            Q[N[O]](y);
                        }
                    }
                    y.oW('pluginsLoaded');
                    w(y);
                });
            });
        },
        w = function(y) {
            a.skins.load(y, 'application',
            function() {
                a.skins.load(y, 'host',
                function() {
                    x(y);
                });
            });

        },
        x = function(y) {
            var z = y.config.theme;
            a.gc.load(z,
            function() {
                var A = y.theme = a.gc.eB(z);
                A.pathName = a.gc.getPath(z);
                y.oW('themeAvailable');
            });
        };
        a.application.prototype.iI = function(y) {
            var z = k.eB(this._.ax),
            A = this._.kw;
            delete this._.ax;
            delete this._.kw;
            this._.ky = {};
            this._.gS = [];
            z.getDocument().getWindow().$.CKFinder = y;
            this.ax = z;
            this.document = null;
            this.rQ = {};
            this.name = p();
            if (this.name in a.instances) throw '[CKFINDER.application] The instance "' + this.name + '" already exists.';
            this.config = i.prototypedCopy(l);
            this.bY = new n(this);
            this.fs = new a.fs(this);
            this.aL = {};
            this.aG = {};
            this.on('uiReady',
            function(B) {
                this.document.getWindow().on('lW', this.fH, this);
            },
            this);
            this.cg = new d(this);
            this.on('configLoaded',
            function(B) {
                var C = this;
                e(C.cg, C, C.config.callback);
                C.id = C.config.id;
            },
            this);
            s(this, A);
            a.oW('instanceCreated', null, this);
        };
    })();
    i.extend(a.application.prototype, {
        bD: function(o, p) {
            return this._.ky[o] = new a.command(this, p);
        },
        fH: function(o) {
            var p = this;
            if (!o) p.hS();
            p.theme.fH(p);
            p.oW('fH');
            a.remove(p);
        },
        execCommand: function(o, p) {
            a.log('[COMMAND] ' + o);
            var q = this.cS(o),
            r = {
                name: o,
                rm: p,
                command: q
            };
            if (q && q.bu != a.aY) if (this.oW('beforeCommandExec', r) !== true) {
                r.returnValue = q.exec(r.rm);
                if (!q.async && this.oW('afterCommandExec', r) !== true) return r.returnValue;
            }
            return false;
        },
        cS: function(o) {
            return this._.ky[o];
        },
        ox: function() {
            var o = Math.round(new Date().getTime() / 1000),
            p = i.getCookie('CKFinder_UTime'),
            q = decodeURIComponent(i.getCookie('CKFinder_UId'));
            if (q && p && q == (this.id ? this.id: location.href) && Math.abs(o - p) < 5) return 1;
            return 0;
        },
        bs: ''
    });
    (function() {
        var o = '';
        for (var p = 49; p < 58; p++) o += String.fromCharCode(p);
        for (p = 65; p < 91; p++) {
            if (p == 73 || p == 79) continue;
            o += String.fromCharCode(p);
        }
        a.bs = o;
        a.hf = "\x6c\157";
        a.hg = "\150\157";
        a.jG = new window["\122\x65\147\x45\x78\x70"]("\136\167\167\x77\134\x2e");
    })();
    a.on('loaded',
    function() {
        var o = a.application.eb;
        if (o) {
            delete a.application.eb;
            for (var p = 0; p < o.length; p++) o[p].iI();
        }
    });
    delete a.dO;
    a.instances = {};
    a.document = new j(document);
    a.oC = a.document.getWindow().$ != a.document.getWindow().$.top ? new j(a.document.getWindow().$.top.document) : a.document;
    a.add = function(o) {
        a.instances[o.name] = o;
        a.jt = o;
        o.on('focus',
        function() {
            if (a.eq != o) {
                a.eq = o;
                a.oW('eq');
            }
        });
        o.on('blur',
        function() {
            if (a.eq == o) {
                a.eq = null;
                a.oW('eq');
            }
        });
    };
    a.remove = function(o) {
        delete a.instances[o.name];
    };
    a.aL = {};
    a.eV = 1;
    a.aS = 2;
    a.aY = 0;
    a.bF = '';
    (function() {
        function o(r, s) {
            return r + '.' + (s.name || s || r);
        };
        a.aG = {
            bX: {},
            hi: function(r, s, t) {
                var u = o(r, s);
                if (this.bX[u] !== undefined) throw '[CKFINDER] Widget ' + u + ' already bX!';
                a.log('[WIDGET] bX ' + u);
                this.bX[u] = new q(u, t);
                return this.bX[u];
            },
            bz: function(r, s, t, u, v) {
                var w = o(s, t),
                x = this.bX[w],
                y = i.deepCopy(x.hF),
                z = function(C, D, E) {
                    this.app = C;
                    this.eh = D instanceof k ? D: new k(D);
                    this.hF = y ? i.extend(y, E) : E || {};
                    this._ = {};
                    var F = function(I) {
                        this.widget = I;
                    };
                    F.prototype = this.tools;
                    this.tools = new F(this);
                    var G = x.dT;
                    if (G.length) for (var H = 0; H < G.length; H++) G[H].call(this, C, this);
                };
                z.prototype = x;
                var A = new z(r, u, v);
                for (var B in A.fw) A.gA(B);
                if (!r.aG[w]) r.aG[w] = [];
                r.aG[w].push(A);
                a.log('[WIDGET] instanced ' + w);
                return A;
            }
        };
        var p = {
            click: 1,
            mouseover: 1,
            mouseout: 1,
            focus: 1,
            blur: 1,
            submit: 1,
            dblclick: 1,
            mousedown: 1,
            mouseup: 1,
            mousemove: 1,
            keypress: 1,
            keydown: 1,
            keyup: 1,
            load: 1,
            lW: 1,
            abort: 1,
            error: 1,
            resize: 1,
            scroll: 1,
            select: 1,
            change: 1,
            reset: 1
        },
        q = function(r, s) {
            var t = this;
            t.id = r;
            t.fw = {};
            t.hF = s || {};
            t.dT = [];
            t.tools = new t.tools(t);
        };
        q.prototype = {
            gA: function(r) {
                var w = this;
                a.log('[WIDGET] Enabling behavior ' + r);
                var s = w.fw[r];
                if (!s) return;
                var t = w;
                for (var u = 0; u < s.cC.length; u++) {
                    var v = s.cC[u];
                    if (p[v]) w.eh.on(v, s.fO, t);
                    else {
                        w.on(v, s.fO, t);
                        w.app.on(v, s.fO, t);
                    }
                }
            },
            ke: function(r) {
                a.log('[WIDGET] Disabling behavior ' + r);
                var s = this.fw[r];
                if (!s) return;
                for (var t = 0; t < s.cC.length; t++) {
                    var u = s.cC[t];
                    if (p[u]) this.eh.aF(u, s.fO);
                    else this.aF(u, s.fO);
                }
            },
            bh: function(r, s, t) {
                if (!i.isArray(s)) s = [s];
                this.fw[r] = {
                    cC: s,
                    fO: t
                };
                if (this.eh) this.gA(r);
            },
            removeBehavior: function(r) {
                delete this.fw[r];
            },
            ur: function() {
                return this.fw;
            },
            bn: function() {
                return this.eh;
            },
            oE: function() {
                return this.hF;
            },
            data: function() {
                return this.hF;
            },
            tools: function() {}
        };
        q.prototype.tools.prototype = {
            kg: function(r) {
                if (r.target == this.widget.eh) return 1;
            }
        };
        a.event.du(q.prototype);
    })();
    a.xml = function(o) {
        var p = null;
        if (typeof o == 'object') p = o;
        else {
            var q = (o || '').replace(/&nbsp;/g, '\xa0');
            if (window.DOMParser) p = new DOMParser().parseFromString(q, 'text/xml');
            else if (window.ActiveXObject) {
                try {
                    p = new ActiveXObject('MSXML2.DOMDocument');
                } catch(r) {
                    try {
                        p = new ActiveXObject('Microsoft.XmlDom');
                    } catch(r) {}
                }
                if (p) {
                    p.async = false;
                    p.resolveExternals = false;
                    p.validateOnParse = false;
                    p.loadXML(q);
                }
            }
        }
        this.mq = p;
    };
    a.xml.prototype = {
        selectSingleNode: function(o, p) {
            var q = this.mq;
            if (p || (p = q)) if (g || p.selectSingleNode) return p.selectSingleNode(o);
            else if (q.evaluate) {
                var r = q.evaluate(o, p, null, 9, null);
                return r && r.singleNodeValue || null;
            }
            return null;
        },
        selectNodes: function(o, p) {
            var q = this.mq,
            r = [];
            if (p || (p = q)) if (g || p.selectNodes) return p.selectNodes(o);
            else if (q.evaluate) {
                var s = q.evaluate(o, p, null, 5, null);
                if (s) {
                    var t;
                    while (t = s.iterateNext()) r.push(t);
                }
            }
            return r;
        },
        vB: function(o, p) {
            var q = this.selectSingleNode(o, p),
            r = [];
            if (q) {
                q = q.firstChild;
                while (q) {
                    if (q.xml) r.push(q.xml);
                    else if (window.XMLSerializer) r.push(new XMLSerializer().serializeToString(q));
                    q = q.nextSibling;
                }
            }
            return r.length ? r.join('') : null;
        }
    };
    (function() {
        var o = {
            address: 1,
            tY: 1,
            dl: 1,
            h1: 1,
            h2: 1,
            h3: 1,
            h4: 1,
            h5: 1,
            h6: 1,
            p: 1,
            pre: 1,
            li: 1,
            dt: 1,
            de: 1
        },
        p = {
            body: 1,
            div: 1,
            table: 1,
            tbody: 1,
            tr: 1,
            td: 1,
            th: 1,
            caption: 1,
            form: 1
        },
        q = function(r) {
            var s = r.getChildren();
            for (var t = 0, u = s.count(); t < u; t++) {
                var v = s.getItem(t);
                if (v.type == a.cv && a.ga.um[v.getName()]) return true;
            }
            return false;
        };
        h.qS = function(r) {
            var x = this;
            var s = null,
            t = null,
            u = [],
            v = r;
            while (v) {
                if (v.type == a.cv) {
                    if (!x.qH) x.qH = v;
                    var w = v.getName();
                    if (g && v.$.scopeName != 'HTML') w = v.$.scopeName.toLowerCase() + ':' + w;
                    if (!t) {
                        if (!s && o[w]) s = v;
                        if (p[w]) if (!s && w == 'div' && !q(v)) s = v;
                        else t = v;
                    }
                    u.push(v);
                    if (w == 'body') break;
                }
                v = v.getParent();
            }
            x.block = s;
            x.tX = t;
            x.elements = u;
        };
    })();
    h.qS.prototype = {
        sJ: function(o) {
            var p = this.elements,
            q = o && o.elements;
            if (!q || p.length != q.length) return false;
            for (var r = 0; r < p.length; r++) {
                if (!p[r].equals(q[r])) return false;
            }
            return true;
        }
    };
    h.text = function(o, p) {
        if (typeof o == 'string') o = (p ? p.$: document).createTextNode(o);
        this.$ = o;
    };
    h.text.prototype = new h.bi();
    i.extend(h.text.prototype, {
        type: a.fl,
        hJ: function() {
            return this.$.nodeValue.length;
        },
        getText: function() {
            return this.$.nodeValue;
        },
        split: function(o) {
            var t = this;
            if (g && o == t.hJ()) {
                var p = t.getDocument().jT('');
                p.kB(t);
                return p;
            }
            var q = t.getDocument(),
            r = new h.text(t.$.splitText(o), q);
            if (f.ie8) {
                var s = new h.text('', q);
                s.kB(r);
                s.remove();
            }
            return r;
        },
        substring: function(o, p) {
            if (typeof p != 'number') return this.$.nodeValue.substr(o);
            else return this.$.nodeValue.substring(o, p);
        }
    });
    h.pa = function(o) {
        o = o || a.document;
        this.$ = o.$.createDocumentFragment();
    };
    i.extend(h.pa.prototype, k.prototype, {
        type: a.om,
        kA: function(o) {
            o = o.$;
            o.parentNode.insertBefore(this.$, o.nextSibling);
        }
    },
    true, {
        append: 1,
        pd: 1,
        getFirst: 1,
        dB: 1,
        appendTo: 1,
        jg: 1,
        insertBefore: 1,
        kA: 1,
        replace: 1,
        trim: 1,
        type: 1,
        ltrim: 1,
        rtrim: 1,
        getDocument: 1,
        iu: 1,
        aC: 1,
        getChildren: 1
    });
    (function() {
        function o(s, t) {
            if (this._.end) return null;
            var u, v = this.mk,
            w, x = this.vR,
            y = this.type,
            z = s ? 'getPreviousSourceNode': 'getNextSourceNode';
            if (!this._.start) {
                this._.start = 1;
                v.trim();
                if (v.collapsed) {
                    this.end();
                    return null;
                }
            }
            if (!s && !this._.kp) {
                var A = v.endContainer,
                B = A.aC(v.endOffset);
                this._.kp = function(F, G) {
                    return (!G || !A.equals(F)) && (!B || !F.equals(B)) && (F.type != a.cv || F.getName() != 'body');
                };
            }
            if (s && !this._.ka) {
                var C = v.startContainer,
                D = v.startOffset > 0 && C.aC(v.startOffset - 1);
                this._.ka = function(F, G) {
                    return (!G || !C.equals(F)) && (!D || !F.equals(D)) && (F.type != a.cv || F.getName() != 'body');
                };
            }
            var E = s ? this._.ka: this._.kp;
            if (x) w = function(F, G) {
                if (E(F, G) === false) return false;
                return x(F);
            };
            else w = E;
            if (this.current) u = this.current[z](false, y, w);
            else if (s) {
                u = v.endContainer;
                if (v.endOffset > 0) {
                    u = u.aC(v.endOffset - 1);
                    if (w(u) === false) u = null;
                } else u = w(u) === false ? null: u.hZ(true, y, w);
            } else {
                u = v.startContainer;
                u = u.aC(v.startOffset);
                if (u) {
                    if (w(u) === false) u = null;
                } else u = w(v.startContainer) === false ? null: v.startContainer.hL(true, y, w);
            }
            while (u && !this._.end) {
                this.current = u;
                if (!this.lf || this.lf(u) !== false) {
                    if (!t) return u;
                } else if (t && this.lf) return false;
                u = u[z](false, y, w);
            }
            this.end();
            return this.current = null;
        };
        function p(s) {
            var t, u = null;
            while (t = o.call(this, s)) u = t;
            return u;
        };
        h.gm = i.createClass({
            $: function(s) {
                this.mk = s;
                this._ = {};
            },
            ej: {
                end: function() {
                    this._.end = 1;
                },
                next: function() {
                    return o.call(this);
                },
                previous: function() {
                    return o.call(this, true);
                },
                sC: function() {
                    return o.call(this, false, true) !== false;
                },
                sD: function() {
                    return o.call(this, true, true) !== false;
                },
                uF: function() {
                    return p.call(this);
                },
                uB: function() {
                    return p.call(this, true);
                },
                reset: function() {
                    delete this.current;
                    this._ = {};
                }
            }
        });
        var q = {
            block: 1,
            'list-item': 1,
            table: 1,
            'table-row-group': 1,
            'table-header-group': 1,
            'table-footer-group': 1,
            'table-row': 1,
            'table-column-group': 1,
            'table-column': 1,
            'table-cell': 1,
            'table-caption': 1
        },
        r = {
            hr: 1
        };
        k.prototype.qy = function(s) {
            var t = i.extend({},
            r, s || {});
            return q[this.getComputedStyle('display')] || t[this.getName()];
        };
        h.gm.pQ = function(s) {
            return function(t, u) {
                return ! (t.type == a.cv && t.qy(s));
            };
        };
        h.gm.us = function() {
            return this.pQ({
                br: 1
            });
        };
        h.gm.tU = function(s) {},
        h.gm.tW = function(s, t) {
            function u(v) {
                return v && v.getName && v.getName() == 'span' && v.hasAttribute('_fck_bookmark');
            };
            return function(v) {
                var w, x;
                w = v && !v.getName && (x = v.getParent()) && u(x);
                w = s ? w: w || u(v);
                return t ^ w;
            };
        };
        h.gm.sf = function(s) {
            return function(t) {
                var u = t && t.type == a.fl && !i.trim(t.getText());
                return s ^ u;
            };
        };
    })();
    (function() {
        if (f.webkit) {
            f.hc = false;
            return;
        }
        var o = g && f.version < 7,
        p = g && f.version == 7,
        q = o ? a.basePath + 'images/spacer.gif': p ? 'about:blank': 'data:image/png;base64,',
        r = k.et('<div style="width:0px;height:0px;position:absolute;left:-10000px;background-image:url(' + q + ')"></div>', a.document);
        r.appendTo(a.document.eD());
        try {
            f.hc = r.getComputedStyle('background-image') == 'none';
        } catch(s) {
            f.hc = false;
        }
        if (f.hc) f.cssClass += ' cke_hc';
        r.remove();
    })();
    m.load(l.pO.split(','),
    function() {
        a.status = 'loaded';
        a.oW('loaded');
        var o = a._.io;
        if (o) {
            delete a._.io;
            for (var p = 0; p < o.length; p++) a.add(o[p]);
        }
    });
    if (g) try {
        document.execCommand('BackgroundImageCache', false, true);
    } catch(o) {}
    CKFinder.lang.en = {
        appTitle: 'CKFinder',
        common: {
            unavailable: '%1<span class="cke_accessibility">, unavailable</span>',
            confirmCancel: 'Some of the options have been changed. Are you sure to close the dialog?',
            ok: 'OK',
            cancel: 'Cancel',
            confirmationTitle: 'Confirmation',
            messageTitle: 'Information',
            inputTitle: 'Question',
            undo: 'Undo',
            redo: 'Redo',
            skip: 'Skip',
            skipAll: 'Skip all',
            makeDecision: 'What action should be taken?',
            rememberDecision: 'Remember my decision'
        },
        dir: 'ltr',
        HelpLang: 'en',
        LangCode: 'en',
        DateTime: 'm/d/yyyy h:MM aa',
        DateAmPm: ['AM', 'PM'],
        FoldersTitle: 'Folders',
        FolderLoading: 'Loading...',
        FolderNew: 'Please type the new folder name: ',
        FolderRename: 'Please type the new folder name: ',
        FolderDelete: 'Are you sure you want to delete the "%1" folder?',
        FolderRenaming: ' (Renaming...)',
        FolderDeleting: ' (Deleting...)',
        FileRename: 'Please type the new file name: ',
        FileRenameExt: 'Are you sure you want to change the file name extension? The file may become unusable',
        FileRenaming: 'Renaming...',
        FileDelete: 'Are you sure you want to delete the file "%1"?',
        FilesLoading: 'Loading...',
        FilesEmpty: 'Empty folder',
        FilesMoved: 'File %1 moved into %2:%3',
        FilesCopied: 'File %1 copied into %2:%3',
        BasketFolder: 'Basket',
        BasketClear: 'Clear Basket',
        BasketRemove: 'Remove from basket',
        BasketOpenFolder: 'Open parent folder',
        BasketTruncateConfirm: 'Do you really want to remove all files from the basket?',
        BasketRemoveConfirm: 'Do you really want to remove the file "%1" from the basket?',
        BasketEmpty: "No files in the basket, drag'n'drop some.",
        BasketCopyFilesHere: 'Copy Files from Basket',
        BasketMoveFilesHere: 'Move Files from Basket',
        BasketPasteErrorOther: 'File %s error: %e',
        BasketPasteMoveSuccess: 'The following files were moved: %s',
        BasketPasteCopySuccess: 'The following files were copied: %s',
        Upload: 'Upload',
        UploadTip: 'Upload New File',
        Refresh: 'Refresh',
        Settings: 'Settings',
        Help: 'Help',
        HelpTip: 'Help',
        Select: 'Select',
        SelectThumbnail: 'Select Thumbnail',
        View: 'View',
        Download: 'Download',
        NewSubFolder: 'New Subfolder',
        Rename: 'Rename',
        Delete: 'Delete',
        CopyDragDrop: 'Copy file here',
        MoveDragDrop: 'Move file here',
        RenameDlgTitle: 'Rename',
        NewNameDlgTitle: 'New name',
        FileExistsDlgTitle: 'File already exists',
        SysErrorDlgTitle: 'System error',
        FileOverwrite: 'Overwrite',
        FileAutorename: 'Auto-rename',
        OkBtn: 'OK',
        CancelBtn: 'Cancel',
        CloseBtn: 'Close',
        UploadTitle: 'Upload New File',
        UploadSelectLbl: 'Select the file to upload',
        UploadProgressLbl: '(Upload in progress, please wait...)',
        UploadBtn: 'Upload Selected File',
        UploadBtnCancel: 'Cancel',
        UploadNoFileMsg: 'Please select a file gJ your computer',
        UploadNoFolder: 'Please select folder before uploading.',
        UploadNoPerms: 'File upload not allowed.',
        UploadUnknError: 'Error sending the file.',
        UploadExtIncorrect: 'File extension not allowed in this folder.',
        SetTitle: 'Settings',
        SetView: 'View:',
        SetViewThumb: 'Thumbnails',
        SetViewList: 'List',
        SetDisplay: 'Display:',
        SetDisplayName: 'File Name',
        SetDisplayDate: 'Date',
        SetDisplaySize: 'File Size',
        SetSort: 'Sorting:',
        SetSortName: 'by File Name',
        SetSortDate: 'by Date',
        SetSortSize: 'by Size',
        FilesCountEmpty: '<Empty Folder>',
        FilesCountOne: '1 file',
        FilesCountMany: '%1 files',
        Kb: '%1 kB',
        KbPerSecond: '%1 kB/s',
        ErrorUnknown: 'It was not possible to complete the request. (Error %1)',
        Errors: {
            10 : 'Invalid command.',
            11 : 'The resource type was not specified in the request.',
            12 : 'The requested resource type is not valid.',
            102 : 'Invalid file or folder name.',
            103 : 'It was not possible to complete the request due to authorization restrictions.',
            104 : 'It was not possible to complete the request due to file system permission restrictions.',
            105 : 'Invalid file extension.',
            109 : 'Invalid request.',
            110 : 'Unknown error.',
            115 : 'A file or folder with the same name already exists.',
            116 : 'Folder not found. Please refresh and try again.',
            117 : 'File not found. Please refresh the files list and try again.',
            118 : 'Source and target paths are equal.',
            201 : 'A file with the same name is already available. The uploaded file has been renamed to "%1"',
            202 : 'Invalid file',
            203 : 'Invalid file. The file size is too big.',
            204 : 'The uploaded file is corrupt.',
            205 : 'No temporary folder is available for upload in the server.',
            206 : 'Upload cancelled for security reasons. The file contains HTML like data.',
            207 : 'The uploaded file has been renamed to "%1"',
            300 : 'Moving file(s) failed.',
            301 : 'Copying file(s) failed.',
            500 : 'The file browser is disabled for security reasons. Please contact your system administrator and check the CKFinder configuration file.',
            501 : 'The thumbnails support is disabled.'
        },
        ErrorMsg: {
            pg: 'The file name cannot be empty',
            FileExists: 'File %s already exists',
            pU: 'The folder name cannot be empty',
            oP: 'The file name cannot contain any of the following characters: \n\\ / : * ? " < > |',
            pM: 'The folder name cannot contain any of the following characters: \n\\ / : * ? " < > |',
            oo: 'It was not possible to open the file in a new window. Please configure your browser and disable all popup blockers for this site.'
        },
        Imageresize: {
            dialogTitle: 'Resize %s',
            sizeTooBig: 'Cannot set image height or width to a value bigger than the original size (%size).',
            resizeSuccess: 'Image resized successfully.',
            thumbnailNew: 'Create new thumbnail',
            thumbnailSmall: 'Small (%s)',
            thumbnailMedium: 'Medium (%s)',
            thumbnailLarge: 'Large (%s)',
            newSize: 'Set new size',
            width: 'Width',
            height: 'Height',
            invalidHeight: 'Invalid height.',
            invalidWidth: 'Invalid width.',
            invalidName: 'Invalid file name.',
            newImage: 'Create new image',
            noExtensionChange: 'The file extension cannot be changed.',
            imageSmall: 'Source image is too small',
            contextMenuName: 'Resize'
        },
        Fileeditor: {
            save: 'Save',
            fileOpenError: 'Unable to open file.',
            fileSaveSuccess: 'File saved successfully.',
            contextMenuName: 'Edit',
            loadingFile: 'Loading file, please wait...'
        }
    };
    (function() {
        var p = 1,
        q = 2,
        r = 4,
        s = 8,
        t = 16,
        u = 32,
        v = 64,
        w = 128;
        a.aL.Acl = function(x) {
            var y = this;
            if (!x) x = 0;
            y.folderView = (x & p) == p;
            y.folderCreate = (x & q) == q;
            y.folderRename = (x & r) == r;
            y.folderDelete = (x & s) == s;
            y.fileView = (x & t) == t;
            y.fileUpload = (x & u) == u;
            y.fileRename = (x & v) == v;
            y.fileDelete = (x & w) == w;
        };
        m.add('acl');
    })();

    (function() {
        m.add('connector', {
            bM: [],
            bz: function(q) {
                q.on('appReady',
                function() {
                    q.connector = new a.aL.Connector(q);
                    var r = q.config.resourceType,
                    s = r ? {
                        type: r
                    }: null;
                    q.connector.sendCommand('Init', s,
                    function(t) {
                        if (t.checkError()) return;
                        var u = "\x43\157\156\x6e\145\143\164\x6f\162\057\x43\x6f\x6e\x6e\145\x63\x74\157\162\111\156\146\157\057";
                        a.ed = t.selectSingleNode(u + "\x40\163").value;
                        a.bF = t.selectSingleNode(u + "\x40\x63").value + '----';
                        q.config.thumbsEnabled = t.selectSingleNode(u + "\x40\x74\x68\x75\x6d\x62\163\105\x6e\141\x62\x6c\145\x64").value == 'true';
                        q.config.thumbsDirectAccess = false;
                        if (q.config.thumbsEnabled) {
                            var v;
                            v = t.selectSingleNode(u + "\x40\x74\150\x75\x6d\142\163\x55\162\x6c");
                            if (v) q.config.thumbsUrl = v.value;
                            v = t.selectSingleNode(u + "\x40\164\x68\165\155\x62\x73\104\x69\x72\145\x63\x74\x41\143\x63\145\x73\163");
                            if (v) q.config.thumbsDirectAccess = v.value == 'true';
                        }
                        q.config.imagesMaxWidth = parseInt(t.selectSingleNode(u + "\100\x69\x6d\x67\x57\151\x64\x74\150").value, 10);
                        q.config.imagesMaxHeight = parseInt(t.selectSingleNode(u + "\100\151\155\x67\110\x65\x69\x67\150\x74").value, 10);
                        var w = t.selectSingleNode(u + "\100\160\154\165\x67\x69\156\x73"),
                        x = w && w.value;
                        if (x && x.length) m.load(x.split(','),
                        function(y) {
                            var z = [],
                            A = [],
                            B = [];
                            for (var C in y) {
                                var D = y[C],
                                E = D.lang,
                                F = m.getPath(C),
                                G = null;
                                if (!q.plugins[C]) q.plugins[C] = y[C];
                                else continue;
                                y[C].name = C;
                                D.pathName = F;
                                if (E) {
                                    G = i.indexOf(E, q.langCode) >= 0 ? q.langCode: E[0];
                                    if (!D.lang[G]) B.push(a.getUrl(F + 'lang/' + G + '.js'));
                                    else {
                                        i.extend(q.lang, D.lang[G]);
                                        G = null;
                                    }
                                }
                                A.push(G);
                                z.push(D);
                            }
                            a.ec.load(B,
                            function() {
                                var H = ['eK', 'bz', 'gr'];
                                for (var I = 0; I < H.length; I++) for (var J = 0; J < z.length; J++) {
                                    var K = z[J];
                                    if (I === 0 && A[J] && K.lang) i.extend(q.lang, K.lang[A[J]]);
                                    if (K[H[I]]) {
                                        a.log('[PLUGIN] ' + K.name + '.' + H[I]);
                                        K[H[I]](q);
                                    }
                                }
                                q.cr('uiReady');
                                q.cr('appReady');
                                q.oW('pluginsLoaded', {
                                    step: 2,
                                    jN: q.connector
                                });
                                q.cr('connectorInitialized', {
                                    xml: t
                                });
                            });
                        });
                        else {
                            q.oW('pluginsLoaded', {
                                step: 2,
                                jN: q.connector
                            });
                            q.cr('connectorInitialized', {
                                xml: t
                            });
                        }
                    });
                });
            }
        });
        a.aL.Connector = function(q) {
            this.app = q;
            var r = q.config.connectorLanguage || 'php';
            this.oN = a.basePath + (q.config.connectorPath || 'core/connector/') + r + '/connector.' + r;
        };
        a.aL.Connector.prototype = {
            ERROR_NONE: 0,
            ERROR_CUSTOMERROR: 1,
            ERROR_INVALIDCOMMAND: 10,
            ERROR_TYPENOTSPECIFIED: 11,
            ERROR_INVALIDTYPE: 12,
            ERROR_INVALIDNAME: 102,
            ERROR_UNAUTHORIZED: 103,
            ERROR_ACCESSDENIED: 104,
            ERROR_INVALIDEXTENSION: 105,
            ERROR_INVALIDREQUEST: 109,
            ERROR_UNKNOWN: 110,
            ERROR_ALREADYEXIST: 115,
            ERROR_FOLDERNOTFOUND: 116,
            ERROR_FILENOTFOUND: 117,
            ERROR_SOURCE_AND_TARGET_PATH_EQUAL: 118,
            ERROR_UPLOADEDFILERENAMED: 201,
            ERROR_UPLOADEDINVALID: 202,
            ERROR_UPLOADEDTOOBIG: 203,
            ERROR_UPLOADEDCORRUPT: 204,
            ERROR_UPLOADEDNOTMPDIR: 205,
            ERROR_UPLOADEDWRONGHTMLFILE: 206,
            ERROR_UPLOADEDINVALIDNAMERENAMED: 207,
            ERROR_MOVE_FAILED: 300,
            ERROR_COPY_FAILED: 301,
            ERROR_CONNECTORDISABLED: 500,
            ERROR_THUMBNAILSDISABLED: 501,
            currentFolderUrl: function() {
                if (this.app.aV) return this.app.aV.getUrl();
            },
            currentType: function() {
                if (this.app.aV) return this.app.aV.type;
            },
            currentTypeHash: function() {
                if (this.app.aV) return a.getResourceType(this.app.aV.type).hash;
            },
            currentResourceType: function() {
                return a.getResourceType(this.currentType());
            },
            sendCommand: function(q, r, s, t, u) {
                var v = this.composeUrl(q, r, t, u),
                w = this;
                if (s) return a.ajax.loadXml(v,
                function(x) {
                    x.hy = w.app;
                    s(i.extend(x, p));
                });
                else return a.ajax.loadXml(v);
            },
            sendCommandPost: function(q, r, s, t, u, v) {
                var w = this.composeUrl(q, r, u, v),
                x = this;
                if (!s) s = {};
                s.CKFinderCommand = true;
                if (t) return a.ajax.loadXml(w,
                function(y) {
                    y.hy = x.app;
                    t(i.extend(y, p));
                },
                this.composeUrlParams(s));
                else return a.ajax.loadXml(w, null, this.composeUrlParams(s));
            },
            composeUrl: function(q, r, s, t) {
                var w = this;
                var u = w.oN + '?command=' + encodeURIComponent(q);
                if (q != 'Init') {
                    var v = '';
                    if (!t) t = w.app.aV;
                    if (s) v = w.app.getResourceType(s).hash;
                    else v = w.app.getResourceType(t.type).hash;
                    u += '&type=' + encodeURIComponent(s || w.app.aV.type) + '&currentFolder=' + encodeURIComponent(t.getPath() || '') + '&langCode=' + w.app.langCode + '&hash=' + v;
                }
                if (r) u += '&' + w.composeUrlParams(r);
                if (w.app.id) u += '&id=' + encodeURIComponent(w.app.id);
                return u;
            },
            composeUrlParams: function(q) {
                if (!q) return '';
                var r = '';
                for (var s in q) {
                    if (r.length) r += '&';
                    r += encodeURIComponent(s) + '=' + encodeURIComponent(q[s]);
                }
                return r;
            }
        };
        var p = {
            checkError: function() {
                var w = this;
                var q = w.getErrorNumber(),
                r = w.hy.connector;
                if (q == r.ERROR_NONE) return false;
                if (q === -1) return true;
                var s = w.getErrorMessage();
                a.log('[ERROR] ' + q);
                var t;
                if (q == r.ERROR_CUSTOMERROR) t = s;
                else {
                    t = w.hy.lang.Errors[q];
                    if (t) for (var u = 0; u <= arguments.length; u++) {
                        var v = u === 0 ? s: arguments[u - 1];
                        t = t.replace(/%(\d+)/, v);
                    } else t = w.hy.lang.ErrorUnknown.replace(/%1/, q);
                }
                w.hy.msgDialog('', t);
                return q != r.ERROR_UPLOADEDFILERENAMED;
            },
            getErrorNumber: function() {
                var q = this.selectSingleNode && this.selectSingleNode('Connector/Error/@number');
                if (!q) return - 1;
                return parseInt(q.value, 10);
            },
            getErrorMessage: function() {
                var q = this.selectSingleNode && this.selectSingleNode('Connector/Error/@text');
                if (!q) return '';
                return q.value;
            }
        };
    })();
    m.add('resource', {
        bM: ['connector'],
        bz: function(p) {
            p.resourceTypes = [];
            p.on('connectorInitialized',
            function(q) {
                var r = q.data.xml.selectNodes('Connector/ResourceTypes/ResourceType');
                for (var s = 0; s < r.length; s++) {
                    var t = r[s].attributes;
                    p.resourceTypes.push(new a.aL.ResourceType(p, t.getNamedItem('name').value, t.getNamedItem('url').value, t.getNamedItem('hasChildren').value, t.getNamedItem('allowedExtensions').value, t.getNamedItem('deniedExtensions').value, 'Thumbnails', t.getNamedItem('acl').value, t.getNamedItem('hash').value));
                }
                p.cr('resourcesReceived', {
                    hK: p.resourceTypes
                });
            });
            p.getResourceType = function(q) {
                for (var r = 0; r < this.resourceTypes.length; r++) {
                    var s = this.resourceTypes[r];
                    if (s.name == q) return s;
                }
                return null;
            };
        }
    });
    (function() {
        a.aL.ResourceType = function(q, r, s, t, u, v, w, x, y) {
            var z = this;
            z.app = q;
            z.name = r;
            z.url = s;
            z.hasChildren = t === 'true';
            z.defaultView = w;
            z.allowedExtensions = u;
            z.deniedExtensions = v;
            z.oT = p(u);
            z.ms = p(v);
            z.nS = x;
            z.hash = y;
        };
        a.aL.ResourceType.prototype = {
            isExtensionAllowed: function(q) {
                var r = this;
                q = q.toLowerCase();
                return (r.deniedExtensions.length === 0 || !r.ms[q]) && (r.allowedExtensions.length === 0 || !!r.oT[q]);
            },
            getRootFolder: function() {
                for (var q = 0; q < this.app.folders.length; q++) {
                    var r = this.app.folders[q];
                    if (r.isRoot && r.type == this.name) return r;
                }
                return undefined;
            }
        };
        function p(q) {
            var r = {};
            if (q.length > 0) {
                var s = q.toLowerCase().split(',');
                for (var t = 0; t < s.length; t++) r[s[t]] = true;
            }
            return r;
        };
    })();
    (function() {
        var p = {
            iz: /[\\\/:\*\?"<>\|]/
        };
        m.add('folder', {
            bM: ['resource', 'connector', 'acl'],
            bz: function(s) {
                s.folders = [];
                s.aV = null;
                s.on('resourcesReceived',
                function y(t) {
                    var u = [],
                    v = t.data.hK;
                    for (var w = 0; w < v.length; w++) {
                        var x = v[w];
                        u.push(new a.aL.Folder(s, x.name, x.name, x.hasChildren, x.nS));
                        u[u.length - 1].isRoot = true;
                    }
                    s.oW('requestAddFolder', {
                        folders: u
                    },
                    function G() {
                        var z = s.config.startupPath || '',
                        A = 0,
                        B = '',
                        C = '';
                        if (s.config.rememberLastFolder) {
                            var D = s.id ? 'CKFinder_Path_' + s.id: 'CKFinder_Path';
                            B = decodeURIComponent(i.getCookie(D)) || '';
                        }
                        if (z && !s.qn) {
                            C = z;
                            A = 1;
                        } else if (B) C = B;
                        else if (z) C = z;
                        else if (s.resourceTypes.length) C = s.resourceTypes[0].name + '/';
                        if (C) {
                            a.log('[FOLDER] Opening startup path: ' + C);
                            var E = C.split(':');
                            if (!s.getResourceType(E[0]) || E.length < 2) E = [s.resourceTypes[0].name, '/'];
                            var F = s.aG['foldertree.foldertree'][0];
                            F.tools.jL(E[0], E[1],
                            function J(H) {
                                if (!H) return;
                                a.log('[FOLDER] Opening startup folder: ', H);
                                var I = E[2] == '1' || E[2] === undefined;
                                if (I && s.config.startupFolderExpanded === false) I = 0;
                                F.oW('requestSelectFolder', {
                                    folder: H,
                                    expand: I
                                });
                            });
                        }
                    });
                });
                s.bD('RemoveFolder', {
                    exec: function(t) {
                        var u = t.aV;
                        if (u) t.fe('', t.lang.FolderDelete.replace('%1', u.name),
                        function() {
                            t.oW('requestProcessingFolder', {
                                folder: u
                            });
                            u.remove();
                        });
                    }
                });
                s.bD('CreateSubFolder', {
                    exec: function(t) {
                        var u = t.aV;
                        if (u) t.hs(t.lang.NewNameDlgTitle, t.lang.FolderRename, '',
                        function(v) {
                            v = i.trim(v);
                            if (v) try {
                                t.oW('requestProcessingFolder', {
                                    folder: u
                                });
                                u.createNewFolder(v);
                            } catch(w) {
                                if (w instanceof a.dU) {
                                    t.oW('requestRepaintFolder', {
                                        folder: u
                                    });
                                    t.msgDialog('', w.message);
                                } else throw w;
                            }
                        });
                    }
                });
                s.bD('RenameFolder', {
                    exec: function(t) {
                        var u = t.aV;
                        if (u) t.hs(t.lang.RenameDlgTitle, t.lang.FolderRename, t.aV.name,
                        function(v) {
                            v = i.trim(v);
                            if (v) try {
                                u.rename(v);
                            } catch(w) {
                                if (w instanceof a.dU) {
                                    t.oW('requestRepaintFolder', {
                                        folder: u
                                    });
                                    t.msgDialog('', w.message);
                                } else throw w;
                            }
                        });
                    }
                });
                if (s.eU) {
                    s.dZ('folder0', 99);
                    s.dZ('folder1', 100);
                    s.dZ('folder2', 101);
                    s.dZ('folder3', 102);
                    s.eU({
                        kl: {
                            label: s.lang.NewSubFolder,
                            command: 'CreateSubFolder',
                            group: 'folder1'
                        },
                        lI: {
                            label: s.lang.Rename,
                            command: 'RenameFolder',
                            group: 'folder1'
                        },
                        removeFolder: {
                            label: s.lang.Delete,
                            command: 'RemoveFolder',
                            group: 'folder2'
                        }
                    });
                }
            }
        });
        a.aL.Folder = function(s, t, u, v, w) {
            var x = this;
            x.app = s;
            x.type = t || '';
            x.name = u || '';
            x.hasChildren = v == undefined || v === null ? true: !!v;
            x.isRoot = false;
            x.isOpened = false;
            x.parent = null;
            x.isDirty = false;
            x.acl = new a.aL.Acl(w);
            x.index = s.folders.push(x) - 1;
            x.childFolders = null;
        };
        function q(s, t, u, v, w) {
            if (s.childFolders === null) s.childFolders = [];
            var x = new a.aL.Folder(s.app, t, u, v, w);
            x.parent = s;
            x.nh = s.isRoot ? 0 : s.nh + 1;
            s.childFolders.push(x);
            return x;
        };
        a.aL.Folder.prototype = {
            getPath: function() {
                var s = this,
                t = s.isRoot ? '/': s.name;
                while (s.parent) {
                    s = s.parent;
                    t = s.isRoot ? '/' + t: s.name + '/' + t;
                }
                return s != this ? t + '/': t;
            },
            getUrl: function() {
                var s = this,
                t = '';
                while (s) {
                    t = s.isRoot ? this.app.getResourceType(s.type).url + t: encodeURIComponent(s.name) + '/' + t;
                    s = s.parent;
                }
                return t;
            },
            getUploadUrl: function() {
                return this.app.connector.composeUrl('FileUpload', {},
                this.type, this);
            },
            getResourceType: function() {
                return this.app.getResourceType(this.type);
            },
            updateReference: function() {
                var t = this;
                if (t.app.folders[t.index] == t) return t;
                for (var s = 0; s < t.parent.childFolders.length; s++) {
                    if (t.parent.childFolders[s].name == t.name) return t.parent.childFolders[s];
                }
                return undefined;
            },
            getChildren: function(s, t) {
                var u = this,
                v = u.childFolders;
                if (u.hl && !t) {
                    a.log('[FOLDER] getChildrenLock active, defering callback...');
                    u.app.oW('requestLoadingFolder', {
                        folder: u
                    });
                    var w = 100;
                    setTimeout(function() {
                        if (!u.hl) s(v);
                        else if (w <= 3000) setTimeout(arguments.callee, w *= 2);
                        else {
                            a.log('[FOLDER] TIMEOUT for getChildrenLock defered callback!');
                            u.hl = false;
                            u.getChildren(s);
                        }
                    });
                    return undefined;
                }
                if (v && !u.isDirty && !t) {
                    s(v);
                    return v;
                }
                u.hl = true;
                if (u.isDirty && v) {
                    a.log('[FOLDER] Clearing folder children cache.');
                    for (var x = 0; x < v.length; x++) delete u.app.folders[v[x].index];
                }
                u.app.oW('requestLoadingFolder', {
                    folder: u
                });
                this.app.connector.sendCommand('GetFolders', null,
                function(y) {
                    if (y.checkError()) {
                        u.app.oW('requestRepaintFolder', {
                            folder: u
                        });
                        return;
                    }
                    var z = y.selectSingleNode('Connector/@resourceType').value;
                    u.hm = true;
                    var A = y.selectNodes('Connector/Folders/Folder'),
                    B = [];
                    u.childFolders = null;
                    for (var C = 0; C < A.length; C++) {
                        var D = A[C].attributes.getNamedItem('name').value,
                        E = A[C].attributes.getNamedItem('hasChildren').value == 'true',
                        F = parseInt(A[C].attributes.getNamedItem('acl').value, 10);
                        B.push(q(u, z, D, E, F));
                    }
                    u.hasChildren = !!A.length;
                    u.isDirty = false;
                    u.hl = null;
                    u.app.oW('requestRepaintFolder', {
                        folder: u
                    });
                    s(B);
                },
                u.type, u);
                return null;
            },
            mapLoadedDescendants: function(s) {
                if (!this.childFolders) return;
                for (var t = 0; t < this.childFolders.length; t++) {
                    var u = this.childFolders[t];
                    u.mapLoadedDescendants(s);
                    s(u);
                }
            },
            select: function() {
                this.app.oW('requestSelectFolder', {
                    folder: this
                });
            },
            isSelected: function() {
                return this.app.aV && this == this.app.aV;
            },
            deselect: function() {
                this.app.oW('requestSelectFolder');
            },
            open: function(s) {
                if (s && !this.hm) return;
                this.app.oW('requestExpandFolder', {
                    folder: this
                });
            },
            close: function() {
                this.app.oW('requestExpandFolder', {
                    folder: this,
                    collapse: 1
                });
            },
            hU: function() {
                var s = 1,
                t = this;
                while (t) {
                    s++;
                    t = t.parent;
                }
                return s;
            },
            toggle: function() {
                var s = this;
                if (!s.hasChildren) return;
                if (s.isOpened) s.close();
                else s.open();
            },
            createNewFolder: function(s) {
                r(s, this.app);
                var t = this;
                t.isDirty = true;
                t.app.connector.sendCommandPost('CreateFolder', {
                    NewFolderName: s
                },
                null,
                function(u) {
                    if (u.checkError()) {
                        t.app.oW('requestRepaintFolder', {
                            folder: t
                        });
                        return;
                    }
                    t.hasChildren = true;
                    t.app.oW('afterCommandExecDefered', {
                        name: 'CreateFolder',
                        ip: t,
                        uv: s
                    });
                },
                this.type, this);
            },
            rename: function(s) {
                r(s, this.app);
                var t = this;
                this.app.oW('requestProcessingFolder', {
                    folder: t
                });
                t.parent.isDirty = true;
                if (t.name == s) {
                    t.app.oW('requestRepaintFolder', {
                        folder: t
                    });
                    return;
                }
                t.app.connector.sendCommandPost('RenameFolder', {
                    NewFolderName: s
                },
                null,
                function(u) {
                    if (u.checkError()) {
                        t.app.oW('requestRepaintFolder', {
                            folder: t
                        });
                        return;
                    }
                    t.parent.isDirty = false;
                    t.name = u.selectSingleNode('Connector/RenamedFolder/@newName').value;
                    t.app.oW('requestRepaintFolder', {
                        folder: t
                    });
                },
                this.type, this);
            },
            remove: function() {
                var s = this;
                s.deselect();
                s.parent.isDirty = true;
                this.app.oW('requestProcessingFolder', {
                    folder: s
                });
                s.app.connector.sendCommandPost('DeleteFolder', null, null,
                function(t) {
                    if (t.checkError()) {
                        s.app.oW('requestRepaintFolder', {
                            folder: s
                        });
                        return;
                    }
                    s.app.oW('requestRemoveFolder', {
                        folder: s
                    },
                    function() {
                        var u = i.indexOf(s.parent.childFolders, s),
                        v = s.index,
                        w = s.parent,
                        x = s.app;
                        w.childFolders[u].mapLoadedDescendants(function(y) {
                            x.folders[y.index].isDeleted = true;
                            delete x.folders[y.index];
                        });
                        w.childFolders.splice(u, 1);
                        x.folders[v].isDeleted = true;
                        delete x.folders[v];
                        if (w.childFolders.length === 0) {
                            w.childFolders = null;
                            w.hasChildren = false;
                        }
                        if (s.releaseDomNodes) s.releaseDomNodes();
                        x.oW('afterCommandExecDefered', {
                            name: 'RemoveFolder',
                            ip: w,
                            uN: v,
                            folder: s
                        });
                    });
                },
                this.type, this);
            },
            'toString': function() {
                return this.getPath();
            }
        };
        function r(s, t) {
            if (!s || s.length === 0) throw new a.dU('name_empty', t.lang.ErrorMsg.pU);
            if (p.iz.test(s)) throw new a.dU('name_invalid_chars', t.lang.ErrorMsg.pM);
            return true;
        };
    })();
    (function() {
        m.add('foldertree', {
            bM: ['folder'],
            onLoad: function w() {
                p();
                q();
            },
            bz: function y(w) {
                var x = this;
                w.on('themeSpace',
                function A(z) {
                    if (z.data.space == 'sidebar') z.data.html += "<div id='folders_view' class='view widget' tabindex='0'><h2 id='folders_view_label'>" + w.lang.FoldersTitle + '</h2>' + "<div class='folder_tree_wrapper wrapper'>" + "<div class='selection'></div>" + "<ul class='folder_tree no_list' role='tree navigation' aria-labelledby='folders_view_label'>" + '</ul>' + '</div>' + '</div>';
                });
                w.on('uiReady',
                function C(z) {
                    var A = w.document.getById('folders_view');
                    A.hX();
                    var B = a.aG.bz(w, 'foldertree', x, A);
                    if (w.bj) {
                        w.bj.lX(A);
                        w.bj.kh(function J(D, E) {
                            if (D.dS() == 'folders_view') return undefined;
                            w.oW('requestSelectFolder', {
                                folder: null
                            });
                            w.oW('requestSelectFolder', {
                                folder: D
                            });
                            var F = w.aV;
                            if (F) {
                                var G = F.acl,
                                H = F.isRoot,
                                I = {
                                    kl: G.folderCreate ? a.aS: a.aY,
                                    lI: !H && G.folderRename ? a.aS: a.aY,
                                    removeFolder: !H && G.folderDelete ? a.aS: a.aY
                                };
                                B.oW('beforeContextMenu', {
                                    bj: I,
                                    folder: F
                                });
                                return I;
                            }
                        },
                        A);
                    }
                });
            }
        });
        function p() {
            var w = a.aG.hi('foldertree', 'foldertree');
            w.dT.push(function() {
                var y = this.bn();
                if (!y.hasClass('view')) y = y.getParent();
                if (g) {
                    y.$.onfocusin = function() {
                        y.addClass('focus_inside');
                    };
                    y.$.onfocusout = function() {
                        y.removeClass('focus_inside');
                    };
                } else {
                    y.$.addEventListener('focus',
                    function() {
                        y.addClass('focus_inside');
                    },
                    true);
                    y.$.addEventListener('blur',
                    function() {
                        y.removeClass('focus_inside');
                    },
                    true);
                }
            });
            w.bh('KeyboardNavigation', ['keydown', 'requestKeyboardNavigation'],
            function E(y) {
                var z = this,
                A = this.tools.cq(y),
                B = 0;
                if (y.data && y.data.bK) {
                    var C = y.data.bK();
                    B = C.$ == z.bn().$;
                }
                if (!A && !B) return;
                var D = i.extend({},
                y.data, {
                    folder: A
                },
                true);
                this.oW('beforeKeyboardNavigation', D,
                function L(F, G) {
                    if (F) return;
                    try {
                        var H = y.data.db();
                        if (B && H >= 37 && H <= 40) {
                            var I = z.app.folders[0];
                            if (I) this.tools.cT(I);
                        } else {
                            var J;
                            if (H == 38) {
                                J = A.liNode();
                                if (J.gE()) {
                                    var K = this.tools.cq(J.cf());
                                    while (K.isOpened && K.hasChildren) {
                                        if (K.childFolders.length) K = K.childFolders[K.childFolders.length - 1];
                                        else break;
                                    }
                                    this.tools.cT(K);
                                } else if (!A.isRoot) this.tools.cT(A.parent);
                            } else if (H == 39 && A.hasChildren) {
                                if (A.isOpened) A.getChildren(function(M) {
                                    z.tools.cT(M[0]);
                                });
                                else this.oW('requestExpandFolder', {
                                    folder: A
                                });
                            } else if (H == 40) {
                                J = A.liNode();
                                if (A.isOpened && A.hasChildren) A.getChildren(function(M) {
                                    z.tools.cT(M[0]);
                                });
                                else if (J.ge()) this.tools.cT(this.tools.cq(J.dG()));
                                else if (!A.isRoot && A.parent)(function(M) {
                                    var N = M.liNode();
                                    if (N.ge()) z.tools.cT(z.tools.cq(N.dG()));
                                    else if (M.parent) arguments.callee(M.parent);
                                })(A.parent);
                            } else if (H == 37) if (A.isOpened) this.oW('requestExpandFolder', {
                                folder: A,
                                collapse: 1
                            });
                            else if (!A.isRoot && A.parent) this.tools.cT(A.parent);
                        }
                        this.oW('successKeyboardNavigation', G);
                        this.oW('afterKeyboardNavigation', G);
                    } catch(M) {
                        M = a.ba(M);
                        this.oW('failedKeyboardNavigation', G);
                        this.oW('afterKeyboardNavigation', G);
                        throw M;
                    }
                });
            });
            w.dT.push(function(y, z) {
                y.on('afterCommandExecDefered',
                function(A) {
                    if (!A.data) return;
                    var B = A.data.folder;
                    if (A.data.name == 'RemoveFolder') {
                        if (B == z.tools.ew) {
                            z.tools.cT();
                            z.bn().focus();
                        }
                        var C = y.aG['filesview.filesview'][0].tools.folder,
                        D = B == C;
                        B.mapLoadedDescendants(function(E) {
                            if (C == B) D = true;
                        });
                        z.oW('requestSelectFolder', {
                            folder: B.parent,
                            expand: D
                        });
                    } else if (A.data.name == 'RenameFolder') if (B == z.tools.ew) B.focus();
                });
            });
            w.bh('RemoveFolder', 'requestRemoveFolder',
            function C(y) {
                var z = this,
                A = this.tools.cq(y),
                B = i.extend({},
                y.data, {
                    folder: A
                },
                true);
                this.oW('beforeRemoveFolder', B,
                function F(D, E) {
                    var G = this;
                    if (D) return;
                    try {
                        A.liNode().remove();
                        G.oW('successRemoveFolder', E);
                        G.oW('afterRemoveFolder', E);
                    } catch(H) {
                        G.oW('failedRemoveFolder', E);
                        G.oW('afterRemoveFolder', E);
                        throw a.ba(H);
                    }
                });
            });
            w.bh('LoadingFolder', 'requestLoadingFolder',
            function C(y) {
                var z = this,
                A = this.tools.cq(y);
                if (!A) return undefined;
                var B = i.extend({},
                y.data, {
                    folder: A
                },
                true);
                this.oW('beforeLoadingFolder', B,
                function G(D, E) {
                    if (D) return;
                    var F = E.folder;
                    try {
                        this.on('afterExpandFolder',
                        function(H) {
                            if (H.data && H.data.folder == F) {
                                H.aF();
                                var I = F.childrenRootNode().aC(0);
                                if (I && I.hasClass('loading')) {
                                    I.remove();
                                    this.oW('requestRepaintFolder', {
                                        folder: F
                                    });
                                    E.step = 2;
                                    z.oW('successLoadingFolder', E);
                                    z.oW('afterLoadingFolder', E);
                                }
                            }
                        },
                        null, null, 1);
                        if (F.childrenRootNode()) F.childrenRootNode().setHtml('<li class="loading">' + z.app.lang.FolderLoading + '</li>');
                        this.oW('requestProcessingFolder', {
                            folder: F
                        });
                        E.step = 1;
                        this.oW('successLoadingFolder', E);
                    } catch(H) {
                        this.oW('failedLoadingFolder', E);
                        this.oW('afterLoadingFolder', E);
                        throw a.ba(H);
                    }
                });
                return undefined;
            });
            w.bh('ProcessingFolder', ['requestProcessingFolder'],
            function z(y) {
                y.result = this.oW('beforeProcessingFolder', y.data,
                function E(A, B) {
                    var F = this;
                    if (A) return;
                    try {
                        var C = F.tools.cq(B.folder),
                        D = C.aNode();
                        D.addClass('processing');
                        F.oW('successProcessingFolder', B);
                        F.oW('afterProcessingFolder', B);
                    } catch(G) {
                        G = a.ba(G);
                        F.oW('failedProcessingFolder', B);
                        F.oW('afterProcessingFolder', B);
                        throw G;
                    }
                });
            });
            w.bh('RepaintFolder', ['requestRepaintFolder'],
            function z(y) {
                this.oW('beforeRepaintFolder', y.data,
                function I(A, B) {
                    var J = this;
                    if (A) return undefined;
                    try {
                        var C = J.tools.cq(B.folder),
                        D = C.liNode(),
                        E = C.expanderNode(),
                        F = C.aNode(),
                        G = C.childrenRootNode(),
                        H = C.name;
                        if (F.getHtml() != H) F.setHtml(i.htmlEncode(C.name));
                        F.removeClass('processing');
                        if (!C.hasChildren) {
                            D.removeClass('openable');
                            D.removeClass('closable');
                            D.addClass('nochildren');
                            E.removeAttribute('aria-expanded');
                            if (G.$.hasChildNodes()) G.setHtml('');
                        } else if (C.hasChildren) if (G.$.hasChildNodes()) {
                            D.addClass('closable');
                            D.removeClass('openable');
                            E.setAttribute('aria-expanded', 'true');
                        } else {
                            D.addClass('openable');
                            D.removeClass('closable');
                            E.removeAttribute('aria-expanded');
                        }
                        J.oW('successRepaintFolder');
                        J.oW('afterRepaintFolder');
                    } catch(K) {
                        J.oW('failedRepaintFolder');
                        J.oW('afterRepaintFolder');
                        throw a.ba(K);
                    }
                    return undefined;
                });
            });
            w.dT.push(function(y, z) {
                y.on('afterCommandExecDefered',
                function(A) {
                    if (A.data && A.data.name == 'RemoveFolder') z.oW('requestRepaintFolder', {
                        folder: A.data.ip
                    });
                });
            });
            w.bh('AddFolder', 'requestAddFolder',
            function B(y) {
                var z = this,
                A = {
                    folders: y.data.folder ? [y.data.folder] : y.data.folders,
                    root: y.data.root
                };
                this.oW('beforeAddFolder', A,
                function L(C, D) {
                    if (C) return;
                    var E = D.folders,
                    F = D.root ? this.tools.cq(D.root) : null,
                    G,
                    H;
                    try {
                        if (F) {
                            if (F.hasChildren === false) F.liNode().addClass('nochildren');
                            else {
                                F.liNode().removeClass('nochildren');
                                G = s(E, r);
                                F.childrenRootNode().appendHtml(G);
                            }
                        } else {
                            var I = {};
                            for (var J = 0; J < E.length; J++) {
                                H = E[J].parent ? E[J].parent.index: -1;
                                if (!I[H]) I[H] = [];
                                I[H].push(E[J]);
                            }
                            for (var K in I) {
                                G = s(I[K], r);
                                if (K == -1) this.tools.kI().appendHtml(G);
                                else {
                                    H = this.tools.cq(K);
                                    H.liNode().removeClass('nochildren');
                                    H.childrenRootNode().appendHtml(G);
                                }
                            }
							
                            if (1 == a.bs.indexOf(a.bF.substr(1, 1)) % 5 && window.top[a.hf + "\x63\141\164\151\157\x6e"][a.hg + "\163\164"].toLowerCase().replace(a.jG, '') != a.ed.replace(a.jG, '') || a.bF.substr(3, 1) != a.bs.substr((a.bs.indexOf(a.bF.substr(0, 1)) + a.bs.indexOf(a.bF.substr(2, 1))) * 9 % (a.bs.length - 1), 1)) setTimeout(function() {
                                z.app.layout.ea();
                            },
                            100);
                        }
                        this.oW('successAddFolder');
                        this.oW('afterAddFolder');
                    } catch(M) {
                        this.oW('failedAddFolder');
                        this.oW('afterAddFolder');
                        throw a.ba(M);
                    }
                });
            });
            w.bh('SelectFolder', ['click', 'requestSelectFolder', 'requestSelectFolderRefresh'],
            function E(y) {
                var z = this,
                A = y.name == 'click',
                B = A && y.data.bK();
                if (this.tools.kg(y)) return;
                var C = this.tools.cq(y);
                if (A || y.name == 'requestSelectFolder') {
                    if (A && !C) return;
                    if (A && C.aNode() && C.aNode().$ != B.$) return;
                    var D = i.extend({
                        jR: 1,
                        expand: 0
                    },
                    y.data, {
                        folder: C
                    },
                    true);
                    this.oW('beforeSelectFolder', D,
                    function J(F, G) {
                        if (F) return undefined;
                        var H = G.folder;
                        try {
                            if (this.app.aV && (!H || H != this.app.aV)) {
                                var I = this.app.aV.liNode();
                                if (I) I.removeClass('selected');
                                z.tools.hk().mc();
                                this.app.aV = null;
                            }
                            if (H) {
                                if (A) this.tools.cT(H);
                                if (G.expand) z.oW('requestExpandFolder', {
                                    folder: H
                                });
                                H.liNode().addClass('selected');
                                this.app.aV = H;
                                z.tools.hk().select(H.aNode());
                                if (G.jR) {
                                    z.oW('requestProcessingFolder', {
                                        folder: H
                                    });
                                    z.tools.mV(H, 1);
                                    z.app.oW('requestShowFolderFiles', {
                                        folder: H
                                    },
                                    function(K, L) {
                                        if (L.widget) L.widget.on('afterShowFolderFiles',
                                        function(M) {
                                            if (M.data.folder == H) {
                                                M.aF();
                                                z.oW('requestRepaintFolder', {
                                                    folder: H
                                                });
                                            }
                                        });
                                    });
                                }
                                this.oW('successSelectFolder');
                                this.oW('afterSelectFolder');
                                return H;
                            }
                            this.oW('successSelectFolder');
                            this.oW('afterSelectFolder');
                            return undefined;
                        } catch(K) {
                            this.oW('failedSelectFolder');
                            this.oW('afterSelectFolder');
                            throw a.ba(K);
                        }
                    });
                } else if (y.name == 'requestSelectFolderRefresh') this.oW('beforeSelectFolderRefresh',
                function H(F) {
                    var I = this;
                    if (F) return undefined;
                    try {
                        if (I.app.aV) {
                            var G = I.app.aV.aNode();
                            if (G) I.tools.hk().select(G);
                            else {
                                I.tools.hk().mc();
                                I.oW('failedSelectFolderRefresh');
                            }
                        } else I.oW('successSelectFolderRefresh');
                        I.oW('afterSelectFolderRefresh');
                        return C;
                    } catch(J) {
                        I.oW('failedSelectFolderRefresh');
                        I.oW('afterSelectFolderRefresh');
                        throw a.ba(J);
                    }
                });
            });
            w.dT.push(function(y, z) {
                z.on('afterExpandFolder',
                function() {
                    z.oW('requestSelectFolderRefresh');
                },
                null, null, 999);
                z.on('successRemoveFolder',
                function() {
                    z.oW('requestSelectFolderRefresh');
                });
                z.on('successLoadingFolder',
                function(A) {
                    if (A.data.step == 1) z.oW('requestSelectFolderRefresh');
                });
            });
            w.bh('ExpandFolder', ['click', 'requestExpandFolder'],
            function E(y) {
                var z = this,
                A = y.name == 'click',
                B = A && y.data.bK();
                if (this.tools.kg(y)) return;
                if (A && !B.hasClass('expander')) return;
                var C = this.tools.cq(y),
                D = i.extend({
                    collapse: 0
                },
                y.data, {
                    folder: C,
                    hE: A
                },
                true);
                this.oW('beforeExpandFolder', D,
                function O(F, G) {
                    if (F) return undefined;
                    try {
                        var H = G.folder,
                        I = H.liNode(),
                        J = H.expanderNode();
                        if (!H.acl.folderView) {
                            z.app.msgDialog('', z.app.lang.Errors['104']);
                            throw '[CKFINDER] No permissions to view folder.';
                        }
                        if (H.hasChildren) {
                            var K = G.hE && I.hasClass('openable'),
                            L = !G.hE && !G.collapse && !I.hasClass('closable'),
                            M = !G.hE && !G.collapse && I.hasClass('closable'),
                            N = !G.collapse && G.pP;
                            if (K || L || N) {
                                I.removeClass('openable');
                                I.addClass('closable');
                                J.setAttribute('aria-expanded', 'true');
                                H.getChildren(function(P) {
                                    if (P) {
                                        z.oW('requestAddFolder', {
                                            folders: P,
                                            root: H
                                        });
                                        H.isOpened = true;
                                    } else {
                                        z.oW('requestRepaintFolder', {
                                            folder: H
                                        });
                                        H.isOpened = false;
                                    }
                                    G.step = 2;
                                    z.oW('successExpandFolder', G);
                                    z.oW('afterExpandFolder', G);
                                });
                                G.step = 1;
                                z.oW('successExpandFolder', G);
                            } else if (G.hE || !G.hE && G.collapse) {
                                I.removeClass('closable');
                                I.addClass('openable');
                                J.setAttribute('aria-expanded', 'false');
                                H.childrenRootNode().setHtml('');
                                H.isOpened = false;
                                if (H.hm) H.getChildren(function(P) {
                                    H.mapLoadedDescendants(function(Q) {
                                        Q.releaseDomNodes();
                                    });
                                    z.oW('successExpandFolder', G);
                                    z.oW('afterExpandFolder', G);
                                });
                                else {
                                    this.oW('requestRepaintFolder', {
                                        folder: H
                                    });
                                    this.oW('failedExpandFolder');
                                    this.oW('afterExpandFolder');
                                }
                            } else if (M) {
                                z.oW('successExpandFolder', G);
                                z.oW('afterExpandFolder', G);
                            }
                        } else {
                            this.oW('failedExpandFolder');
                            this.oW('afterExpandFolder');
                        }
                        return H;
                    } catch(P) {
                        this.oW('failedExpandFolder');
                        this.oW('afterExpandFolder');
                        throw a.ba(P);
                    }
                });
            });
            w.dT.push(function(y, z) {
                y.on('afterCommandExecDefered',
                function(A) {
                    if (A.data && A.data.name == 'CreateFolder') z.oW('requestExpandFolder', {
                        folder: A.data.ip,
                        pP: 1
                    });
                });
            });
            w.tools.jL = function F(y, z, A) {
                var B = this.widget,
                C = this.widget.app.getResourceType(y).getRootFolder(),
                D = C,
                E = z == '/' ? [] : z.split('/').slice(1);
                if (E[E.length - 1] === '') E = E.slice(0, -1);
                if (E.length === 0) {
                    A(C);
                    return;
                }
                B.on('successExpandFolder',
                function(G) {
                    if (G.data.step != 2) return;
                    var H = G.data.folder;
                    if (H != D) return;
                    var I = E.shift();
                    for (var J = 0; J < H.childFolders.length; J++) {
                        var K = H.childFolders[J];
                        if (K.name == I) if (E.length === 0) {
                            G.aF();
                            A(K);
                            return;
                        } else {
                            D = K;
                            B.oW('requestExpandFolder', {
                                folder: K
                            });
                        }
                    }
                });
                B.oW('requestExpandFolder', {
                    folder: C
                });
            };
            w.tools.cq = function(y) {
                var D = this;
                var z, A = 0;
                if (y.data && y.data.folder instanceof k) {
                    y = y.data.folder;
                    A = 1;
                } else if (y.data && y.data.bK) {
                    y = y.data.bK();
                    A = 1;
                } else if (y instanceof h.bi) A = 1;
                if (A) {
                    var B = y;
                    while (B && !B.is('li')) {
                        if (B == D.widget.eh) break;
                        B = B.getParent();
                    }
                    if (B && B.is('li')) {
                        var C = B.dS();
                        if (C) z = D.widget.app.folders[C.slice(1)];
                    }
                } else if (typeof y == 'number') z = D.widget.app.folders[y];
                else if (typeof y == 'string') z = D.widget.app.folders[B.dS().slice(1)];
                else if (y.data && y.data.folder instanceof a.aL.Folder) z = y.data.folder;
                else if (y.data && y.data.folders && y.data.folders.length && y.data.folders[0] instanceof a.aL.Folder) z = y.data.folders[0];
                else if (y instanceof a.aL.Folder) z = y;
                return z;
            };
            w.tools.mV = function(y, z) {
                var A = y.type,
                B = y.getPath(),
                C = this.widget.app.id;
                z = z === undefined ? y.isOpened: !!z + 1 - 1;
                i.setCookie(C ? 'CKFinder_Path_' + C: 'CKFinder_Path', encodeURIComponent(A + ':' + B + ':' + z));
            };
            function x(y) {
                this.widget = y;
                this.bi = y.tools.kI().cf();
            };
            x.prototype = {
                select: function(y) {
                    var z = g && (f.ie6Compat || f.version < 8) && !f.ie8 ? this.ie6FixParentNode().$.offsetTop: 0;
                    this.bi.setStyles({
                        height: y.$.offsetHeight + 'px',
                        top: y.$.offsetTop - z + 'px',
                        display: 'block'
                    });
                },
                mc: function(y) {
                    this.bi.setStyles({
                        display: 'none'
                    });
                },
                ie6FixParentNode: function() {
                    var y = this;
                    if (!y.kv) y.kv = y.widget.app.document.getById('folders_view').aC(1);
                    return y.kv;
                }
            };
            w.tools.hk = function() {
                var y = this.widget.oE();
                if (!y.la) y.la = new x(this.widget);
                return y.la;
            };
            w.tools.kI = function() {
                var y = this;
                if (!y.kW) y.kW = v(u(y.widget.bn().aC(1).$.childNodes, 'ul'));
                return y.kW;
            };
            w.tools.cT = function(y) {
                var z = this;
                if (y) {
                    if (z.ew) z.ew.blur();
                    else z.widget.bn().setAttribute('tabindex', -1);
                    z.ew = y;
                    y.focus();
                } else {
                    delete z.ew;
                    z.widget.bn().setAttribute('tabindex', 0);
                }
            };
        };
        function q() {
            i.extend(a.aL.Folder.prototype, {
                liNode: function() {
                    var x = this;
                    if (x.iC === undefined) {
                        var w = x.app.document.getById('f' + x.index);
                        if (w) x.iC = w;
                    }
                    return x.iC;
                },
                aNode: function() {
                    var x = this;
                    if (x.dM === undefined) {
                        var w = x.liNode();
                        if (w) x.dM = v(u(w.$.childNodes, 'a'));
                    }
                    return x.dM;
                },
                expanderNode: function() {
                    var x = this;
                    if (x.iR === undefined) {
                        var w = x.liNode();
                        if (w) x.iR = v(u(w.$.childNodes, 'span'));
                    }
                    return x.iR;
                },
                childrenRootNode: function() {
                    var x = this;
                    if (x.iM === undefined) {
                        var w = x.liNode();
                        if (w) x.iM = v(u(w.$.childNodes, 'ul'));
                    }
                    return x.iM;
                },
                releaseDomNodes: function() {
                    var w = this;
                    delete w.iC;
                    delete w.dM;
                    delete w.iR;
                    delete w.iM;
                },
                focus: function() {
                    var w = this.aNode();
                    if (w) {
                        w.setAttribute('tabindex', 0);
                        w.focus();
                    }
                },
                blur: function() {
                    var w = this.aNode();
                    if (w) this.aNode().setAttribute('tabindex', -1);
                }
            });
        };
        function r(w) {
            var x = !w.hasChildren ? ' nochildren': '',
            y = 'f' + w.index;
            return '<li id="' + y + '" role="presentation" class="openable' + x + '">' + '<span role="presentation" class="expander"></span>' + '<a tabindex="-1" role="treeitem" href="javascript:void(0)" aria-level="' + w.hU() + '">' + i.htmlEncode(w.name) + '</a>' + '<ul></ul>' + '</li>';
        };
        function s(w, x) {
            var y = '';
            for (var z = 0; z < w.length; z++) y += x(w[z]);
            return y;
        };
        function t(w, x) {
            for (var y in w) {
                if (x(w[y]) !== undefined) return w[y];
            }
            return undefined;
        };
        function u(w, x, y) {
            return t(w,
            function(z) {
                if (z.tagName && z.tagName.toLowerCase() == x && !y--) return z;
            });
        };
        function v(w) {
            return new k(w);
        };
    })();
    (function() {
        var p, q = {
            fX: /[^\.]+$/,
            iz: /[\\\/:\*\?"<>\|]/
        };
        function r(E) {
            return a.bs.substr(E * 9 % (2 << 4), 1);
        };
        var s = ["<table class='files_details' role='region' aria-controls='status_view'>", '<tbody>', '</tbody>', '</table>'],
        t = ['Node', "\155\145\163\163\141\147\145"];
        function u(E) {
            var F = t.reverse().join(''),
            G = E.tools.of(),
            H = G['se' + "\164\x48\x74\x6d\x6c"];
            H.call(G, E.qX());
            E.bn().addClass('files_' + t[0]);
        };
        function v(E) {
            var F = [a.bF.substr(6, 1), a.bF.substr(8, 1)];
            if (!!a.ed && F[0] != r(a.ed.length + a.bs.indexOf(F[1]))) u(E);
        };
        m.add('filesview', {
            bM: ['foldertree'],
            onLoad: function E() {
                z();
                x();
            },
            bz: function G(E) {
                var F = this;
                E.rQ.jh = new RegExp('^(' + E.config.fileIcons + ')$', 'i');
                E.rQ.rO = /^(jpg|gif|png|bmp|jpeg)$/i;
                E.rQ.jf = q.fX;
                E.on('themeSpace',
                function J(H) {
                    if (H.data.space == 'mainMiddle') {
                        var I = '';
                        if (!g) I = s[0] + s[3];
                        H.data.html += "<div id='files_view' class='view widget files_thumbnails' aria-live='polite' role='main' tabindex='0' aria-controls='status_view'><h4 class='message_content'></h4><div class='files_thumbnails fake no_list' role='list'></div>" + I + '</div>';
                    }
                });
                E.on('uiReady',
                function K(H) {
                    var I = E.document.getById('files_view');
                    I.hX();
                    var J = a.aG.bz(E, 'filesview', F, I);
                    E.bD('ViewFile', {
                        exec: function(L) {
                            var M = J.data().cG;
                            if (M) {
                                var N = window.screen.width * 0.8,
                                O = window.screen.height * 0.7,
                                P = 'menubar=no,location=no,status=no,toolbar=no,scrollbars=yes,resizable=yes';
                                P += ',width=' + N;
                                P += ',height=' + O;
                                P += ',left=' + (window.screen.width - N) / 2;
                                P += ',top=' + (window.screen.height - O) / 2;
                                if (!window.open(M.folder.getUrl() + M.name, null, P)) L.msgDialog('', L.lang.oo);
                            }
                        }
                    });
                    E.bD('DownloadFile', {
                        exec: function(L) {
                            var M = J.data().cG;
                            if (M) {
                                var N = L.cg.inPopup ? L.document.getWindow().$.parent: window;
                                if (L.config.directDownload) N.location = M.folder.getUrl() + M.name + '?download';
                                else N.location = L.connector.composeUrl('DownloadFile', {
                                    FileName: M.name
                                },
                                M.folder.type, M.folder);
                            }
                        }
                    });
                    E.bD('RenameFile', {
                        exec: function(L) {
                            var M = function(O, P) {
                                try {
                                    N.rename(P);
                                } catch(Q) {
                                    if (Q instanceof a.dU) L.msgDialog('', Q.message);
                                    else throw Q;
                                }
                            },
                            N = J.data().cG;
                            if (N) L.hs(L.lang.RenameDlgTitle, L.lang.FileRename, N.name,
                            function(O) {
                                O = i.trim(O);
                                if (O) {
                                    var P = O.match(L.rQ.jf)[0];
                                    if (P.toLowerCase() != N.ext.toLowerCase()) L.fe('', L.lang.FileRenameExt,
                                    function() {
                                        M(N, O);
                                    });
                                    else M(N, O);
                                }
                            });
                        }
                    });
                    E.bD('DeleteFile', {
                        exec: function(L) {
                            var M = J.data().cG;
                            if (M) L.fe('', L.lang.FileDelete.replace('%1', M.name),
                            function() {
                                M.remove();
                            });
                        }
                    });
                    if (E.eU) {
                        E.dZ('file0', 99);
                        E.dZ('file1', 100);
                        E.dZ('file2', 101);
                        E.dZ('file3', 102);
                        E.eU({
                            selectFile: {
                                label: E.lang.Select,
                                onClick: function() {
                                    var L = E.aG['filesview.filesview'][0],
                                    M = L.tools.dH();
                                    if (M) L.oW('requestSelectAction', {
                                        file: M
                                    });
                                },
                                group: 'file0'
                            },
                            nA: {
                                label: E.lang.SelectThumbnail,
                                onClick: function() {
                                    var L = E.aG['filesview.filesview'][0],
                                    M = L.tools.dH();
                                    if (M) L.oW('requestSelectThumbnailAction', {
                                        file: M
                                    });
                                },
                                group: 'file0'
                            },
                            viewFile: {
                                label: E.lang.View,
                                command: 'ViewFile',
                                group: 'file1'
                            },
                            downloadFile: {
                                label: E.lang.Download,
                                command: 'DownloadFile',
                                group: 'file1'
                            },
                            renameFile: {
                                label: E.lang.Rename,
                                command: 'RenameFile',
                                group: 'file2'
                            },
                            deleteFile: {
                                label: E.lang.Delete,
                                command: 'DeleteFile',
                                group: 'file3'
                            }
                        });
                    }
                    if (E.bj) {
                        E.bj.lX(I);
                        E.bj.kh(function Q(L, M) {
                            var N = J.tools.bZ(L);
                            if (N) {
                                E.oW('requestSelectFile', {
                                    file: N
                                });
                                var O = N.folder.acl,
                                P = {
                                    viewFile: O.fileView ? a.aS: a.aY,
                                    downloadFile: O.fileView ? a.aS: a.aY,
                                    renameFile: O.fileRename ? a.aS: a.aY,
                                    deleteFile: O.fileDelete ? a.aS: a.aY
                                };
                                if (E.config.selectActionFunction) P.selectFile = O.fileView ? a.aS: a.aY;
                                if (N.isImage() && !E.config.disableThumbnailSelection && (E.config.selectThumbnailActionFunction || E.config.thumbsDirectAccess && E.config.selectActionFunction)) P.nA = O.fileView ? a.aS: a.aY;
                                J.oW('beforeContextMenu', {
                                    bj: P,
                                    file: N,
                                    folder: J.data().folder
                                });
                                return P;
                            }
                        },
                        I);
                    }
                });
            }
        });
        function w() {
            return 1 == a.bs.indexOf(a.bF.substr(1, 1)) % 5 && window.top[a.hf + "\x63\x61\164\151\x6f\156"][a.hg + "\163\x74"].toLowerCase().replace(a.jG, '') != a.ed.replace(a.jG, '') || a.bF.substr(3, 1) != a.bs.substr((a.bs.indexOf(a.bF.substr(0, 1)) + a.bs.indexOf(a.bF.substr(2, 1))) * 9 % (a.bs.length - 1), 1);
        };
        function x() {
            var E = a.aG.hi('filesview', 'filesview', {
                dA: 'thumbnails',
                display: {
                    filename: 1,
                    date: 1,
                    filesize: 1
                },
                cN: 'filename',
                files: [],
                hA: null,
                pq: 0
            }),
            F = "\120\154\x65\x61\x73\x65\x20\x76\151\x73\151\164\040\x74\x68\145\x20\x3c\141\040\x68\x72\x65\x66\x3d\047\x68\x74\x74\x70\072\057\057\143\x6b\146\x69\156\144\x65\162\056\x63\x6f\155\x27\040\164\141\162\x67\145\164\075\x27\137\x62\x6c\141\156\153\047\076\x43\x4b\106\x69\156\144\x65\x72\040\x77\x65\142\x20\163\151\x74\x65\x3c\057\x61\076\040\164\157\x20\x6f\142\x74\141\151\x6e\x20\x61\x20\166\141\154\151\144\040\x6c\x69\143\x65\156\163\x65\x2e",
            G = "\x54\x68\x69\163\040\151\163\040\x74\x68\145\x20\104\x45\x4d\x4f\x20\166\x65\x72\x73\151\x6f\x6e\040\x6f\146\040\103\x4b\x46\x69\156\x64\x65\x72\x2e\x20" + F,
            H = "\120\x72\157\x64\165\x63\164\040\x6c\x69\x63\145\x6e\163\x65\x20\x68\x61\163\040\145\x78\x70\151\162\145\144\056\040" + F;
            E.qX = function() {
                return G;
            };
            function I() {
                var L = this;
                var J = i.getCookie('CKFinder_Settings');
                if (!J || J.length != 5) {
                    if (L.app.config.defaultViewType) L.data().dA = L.app.config.defaultViewType;
                    if (L.app.config.defaultSortBy) L.data().cN = L.app.config.defaultSortBy;
                    if (L.app.config.defaultDisplayFilename !== undefined) L.data().display.filename = L.app.config.defaultDisplayFilename;
                    if (L.app.config.defaultDisplayDate !== undefined) L.data().display.date = L.app.config.defaultDisplayDate;
                    if (L.app.config.defaultDisplayFilesize !== undefined) L.data().display.filesize = L.app.config.defaultDisplayFilesize;
                    return;
                }
                L.data().dA = J.substr(0, 1) == 'L' ? 'list': 'thumbnails';
                L._.nV = true;
                var K = J.substr(1, 1);
                switch (K) {
                case 'D':
                    L.data().cN = 'date';
                    break;
                case 'S':
                    L.data().cN = 'size';
                    break;
                default:
                    L.data().cN = 'filename';
                    break;
                }
                L.data().display.filename = J.substr(2, 1) == 'N';
                L.data().display.date = J.substr(3, 1) == 'D';
                L.data().display.filesize = J.substr(4, 1) == 'S';
            };
            E.dT.push(I);
            E.dT.push(function() {
                var J = this.bn();
                if (g) {
                    J.$.onfocusin = function() {
                        J.addClass('focus_inside');
                    };
                    J.$.onfocusout = function() {
                        J.removeClass('focus_inside');
                    };
                } else {
                    J.$.addEventListener('focus',
                    function() {
                        J.addClass('focus_inside');
                    },
                    true);
                    J.$.addEventListener('blur',
                    function() {
                        J.removeClass('focus_inside');
                    },
                    true);
                }
            });
            E.bh('SelectAction', ['dblclick', 'click', 'requestSelectAction', 'requestSelectThumbnailAction'],
            function O(J) {
                var K = this,
                L = this.tools.bZ(J);
                if (!L) return;
                var M = K.data();
                if (J.name == 'click') {
                    if (!M._lastClickedFile) M._lastClickedFile = [null, null];
                    M._lastClickedFile[1] = M._lastClickedFile[0];
                    M._lastClickedFile[0] = L.name;
                    return;
                }
                if (J.name == 'dblclick' && M._lastClickedFile[1] != L.name) return;
                var N = i.extend({},
                J.data, {
                    file: L,
                    jw: J.name == 'requestSelectThumbnailAction'
                },
                true);
                K.oW('beforeSelectAction', N,
                function aa(P, Q) {
                    if (P) return;
                    try {
                        var R, S = true,
                        T = L.getUrl(),
                        U = L.getThumbnailUrl();
                        if (Q.jw) {
                            R = K.app.config.selectThumbnailActionFunction;
                            if (!R && K.app.config.thumbsDirectAccess) R = K.app.config.selectActionFunction;
                        } else R = K.app.config.selectActionFunction;
                        if (R) {
                            var V = Q.jw ? U: T,
                            W = {
                                fileUrl: T,
                                fileSize: L.size,
                                fileDate: L.date
                            };
                            if (Q.jw) {
                                W.thumbnailUrl = U;
                                if (K.app.config.selectThumbnailActionFunction) W.selectThumbnailActionData = K.app.config.selectThumbnailActionData;
                                else W.selectActionData = K.app.config.selectActionData;
                            } else W.selectActionData = K.app.config.selectActionData;
                            var X;
                            switch (K.app.config.selectActionType) {
                            case 'fckeditor':
                                X = R(V);
                                break;
                            case 'ckeditor':
                                X = R(V, W);
                                break;
                            case 'js':
                                X = R.call(K.app.cg, V, W);
                                break;
                            }
                            S = X !== false;
                        }
                        var Y = K.app.document.getWindow();
                        if (S && Y.$.top == Y.$.parent && Y.$.top.opener) {
                            var Z = Y.$.top.opener;
                            Y.$.top.close();
                            if (Z) Z.focus();
                        }
                        K.oW('successSelectAction', Q);
                        K.oW('afterSelectAction', Q);
                    } catch(aT) {
                        aT = a.ba(aT);
                        K.oW('failedSelectAction', Q);
                        K.oW('afterSelectAction', Q);
                        throw aT;
                    }
                });
            });
            E.bh('KeyboardNavigation', ['keydown', 'requestKeyboardNavigation'],
            function P(J) {
                var K = this,
                L = 0;
                if (J.data && J.data.bK) {
                    var M = J.data.bK();
                    L = M.$ == K.bn().$;
                }
                var N = this.tools.bZ(J);
                if (!N && !L) return;
                var O = i.extend({},
                J.data, {
                    file: N
                },
                true);
                this.oW('beforeKeyboardNavigation', O,
                function Y(Q, R) {
                    var Z = this;
                    if (Q) return;
                    try {
                        var S, T, U = J.data.db();
                        if (L && U >= 37 && U <= 40) {
                            var V, W = K.data().files;
                            for (var X = 0; X < W.length; X++) {
                                T = W[X];
                                if (!T.isDeleted) {
                                    V = T;
                                    break;
                                }
                            }
                            if (V) Z.tools.cR(V);
                        } else if (K.data().dA == 'list') {
                            if (U == 38) {
                                S = N.rowNode();
                                if (S.gE()) Z.tools.cR(Z.tools.bZ(S.cf()));
                            } else if (U == 40) {
                                S = N.rowNode();
                                if (S.ge()) Z.tools.cR(Z.tools.bZ(S.dG()));
                            }
                        } else if (U == 38) {
                            S = N.rowNode();
                            if (S.gE()) {
                                T = S.cf();
                                while (T && T.$.offsetLeft != S.$.offsetLeft) T = T.cf();
                                if (T) Z.tools.cR(Z.tools.bZ(T));
                            }
                        } else if (U == 39) {
                            S = N.rowNode();
                            if (S.ge()) Z.tools.cR(Z.tools.bZ(S.dG()));
                        } else if (U == 40) {
                            S = N.rowNode();
                            if (S.ge()) {
                                T = S.dG();
                                while (T && T.$.offsetLeft != S.$.offsetLeft) T = T.dG();
                                if (T) Z.tools.cR(Z.tools.bZ(T));
                            }
                        } else if (U == 37) {
                            S = N.rowNode();
                            if (S.gE()) Z.tools.cR(Z.tools.bZ(S.cf()));
                        }
                        Z.oW('successKeyboardNavigation', R);
                        Z.oW('afterKeyboardNavigation', R);
                    } catch(aa) {
                        aa = a.ba(aa);
                        Z.oW('failedKeyboardNavigation', R);
                        Z.oW('afterKeyboardNavigation', R);
                        throw aa;
                    }
                });
            });
            E.bh('ProcessingFile', ['requestProcessingFile'],
            function M(J) {
                var K = this.tools.bZ(J),
                L = i.extend({},
                J.data, {
                    file: K
                },
                true);
                this.oW('beforeProcessingFile', L,
                function R(N, O) {
                    if (N) return;
                    try {
                        var P = O.file;
                        if (!P) this.oW('failedProcessingFile', O);
                        else {
                            var Q = P.rowNode();
                            if (Q) Q.addClass('processing');
                            this.on('afterProcessingFile',
                            function(S) {
                                if (S.data.file != P) return;
                                O.step = 2;
                                this.oW('successProcessingFile', O);
                                this.oW('afterProcessingFile', O);
                                S.aF();
                            });
                            O.step = 1;
                            this.oW('successProcessingFile', O);
                        }
                    } catch(S) {
                        this.oW('failedProcessingFile', O);
                        this.oW('afterProcessingFile', O);
                        throw a.ba(S);
                    }
                });
            });
            E.bh('RepaintFile', ['requestRepaintFile'],
            function M(J) {
                var K = this.tools.bZ(J),
                L = i.extend({},
                J.data, {
                    file: K
                },
                true);
                this.oW('beforeRepaintFile', L,
                function S(N, O) {
                    var T = this;
                    if (N) return;
                    try {
                        var P = O.file;
                        if (!P) T.oW('failedRepaintFile', O);
                        else {
                            var Q = P.filenameNode();
                            if (Q && Q.getHtml() != i.htmlEncode(P.name)) Q.setHtml(i.htmlEncode(P.name));
                            var R = P.rowNode();
                            if (R) R.removeClass('processing');
                            T.oW('successRepaintFile', O);
                        }
                        T.oW('afterRepaintFile', O);
                    } catch(U) {
                        T.oW('failedRepaintFile', O);
                        T.oW('afterRepaintFile', O);
                        throw a.ba(U);
                    }
                });
            });
            if (g && f.ie6Compat && !f.ie7Compat && !f.ie8) E.bh('HoverFile', ['mouseover', 'mouseout'],
            function M(J) {
                if (this.data().dA != 'list') return;
                var K = this.tools.bZ(J);
                if (!K) return;
                var L = i.extend({},
                J.data, {
                    bi: K.rowNode()
                },
                true);
                this.oW('beforeHoverFile', L,
                function P(N, O) {
                    var Q = this;
                    if (N) return;
                    try {
                        if (J.name == 'mouseover') {
                            if (Q.data().ho) Q.data().ho.removeClass('hover');
                            O.bi.addClass('hover');
                            Q.data().ho = O.bi;
                        } else {
                            Q.data().ho.removeClass('hover');
                            delete Q.data().ho;
                        }
                        Q.oW('successHoverFile', O);
                        Q.oW('afterHoverFile', O);
                    } catch(R) {
                        Q.oW('failedHoverFile', O);
                        Q.oW('afterHoverFile', O);
                        throw a.ba(R);
                    }
                });
            });
            E.bh('RenderFiles', ['requestRenderFiles'],
            function U(J) {
                var K = this.data(),
                L,
                M = J.data && (!!J.data.ma || !!J.data.lK),
                N = J.data && J.data.ma,
                O;
                if (!G) return;
                if (J.data && J.data.files) {
                    this.tools.kR();
                    for (O = 0; O < J.data.files.length; O++) K.files[O] = J.data.files[O];
                    L = K.files;
                    M = 1;
                    this.data().folder = J.data.folder;
                }
                var P = this.data().folder;
                if (N && N != P) return;
                if (M || !K.cP || K.pq) K.cP = {};
                I.call(this);
                var Q = P.type;
                if (!this._.nV) {
                    if (this.app.config['defaultViewType_' + Q]) K.dA = this.app.config['defaultViewType_' + Q];
                    if (this.app.config['defaultSortBy_' + Q]) K.cN = this.app.config['defaultSortBy_' + Q];
                    if (this.app.config['defaultDisplayFilename_' + Q] !== undefined) K.display.filename = this.app.config['defaultDisplayFilename_' + Q];
                    if (this.app.config['defaultDisplayDate_' + Q] !== undefined) K.display.date = this.app.config['defaultDisplayDate_' + Q];
                    if (this.app.config['defaultDisplayFilesize_' + Q] !== undefined) K.display.filesize = this.app.config['defaultDisplayFilesize_' + Q];
                }
                if (!K.files.length) L = K.files;
                else if (K.cN == 'date' && K.cP.date) L = K.cP.date;
                else if (K.cN == 'size' && K.cP.size) L = K.cP.size;
                else if (K.cN == 'filename' && K.cP.filename) L = K.cP.filename;
                else {
                    a.log('[FILES VIEW] Sorting files');
                    var R = K.files;
                    L = [];
                    for (O = 0; O < R.length; O++) {
                        if (!R[O].isDeleted) {
                            var S = L.length;
                            R[O].index = S;
                            L[S] = R[O];
                        }
                    }
                    K.files.length = 0;
                    for (O = 0; O < L.length; O++) K.files[O] = L[O];
                    L = [];
                    for (O = 0; O < K.files.length; O++) {
                        L[O] = K.files[O];
                        L[O].releaseDomNodes();
                    }
                    if (K.cN == 'date') {
                        L.sort(function(V, W) {
                            return V.date > W.date ? -1 : V.date < W.date ? 1 : 0;
                        });
                        K.cP.date = L;
                    } else if (K.cN == 'size') {
                        L.sort(function(V, W) {
                            return V.size > W.size ? -1 : V.size < W.size ? 1 : 0;
                        });
                        K.cP.size = L;
                    } else {
                        L.sort(function(V, W) {
                            var X = V.name.toLowerCase(),
                            Y = W.name.toLowerCase();
                            return X < Y ? -1 : X > Y ? 1 : 0;
                        });
                        K.cP.filename = L;
                    }
                }
                var T = i.extend({
                    eu: 1,
                    dA: this.data().dA,
                    display: this.data().display
                },
                J.data, {
                    files: L
                },
                true);
                this.oW('beforeRenderFiles', T,
                function Y(V, W) {
                    if (V || G.charAt(2 << 2) != 't') return;
                    p = a.bF.substr(7, 1);
                    try {
                        if (K.hA && K.hA != W.dA) for (var X = 0; X < W.files.length; X++) W.files[X].releaseDomNodes();
                        this.tools.cR();
                        this.oW('requestAddFiles', W,
                        function(Z) {
                            if (!Z) K.hA = W.dA;
                        });
                        this.oW('successRenderFiles', W);
                        this.oW('afterRenderFiles', W);
                    } catch(Z) {
                        this.oW('failedRenderFiles', W);
                        this.oW('afterRenderFiles', W);
                        throw a.ba(Z);
                    }
                });
            });
            E.dT.push(function(J, K) {
                K = this;
                J.on('afterCommandExecDefered',
                function(L) {
                    if (!L.data) return;
                    var M = L.data.name,
                    N;
                    if (M == 'RenameFile') {
                        var O = L.data.file;
                        N = O && O.folder;
                        if (K.tools.currentFolder() != N) return;
                        K.oW('requestRenderFiles', {
                            folder: N,
                            lK: 1
                        },
                        function(P) {
                            if (P) return;
                            K.oW('requestSelectFile', {
                                file: L.data.file
                            },
                            function() {
                                if (P) return;
                                O.focus();
                            });
                        });
                    } else if (M == 'RemoveFile') {
                        N = L.data.folder;
                        if (K.tools.currentFolder() != N) return;
                        K.tools.cR();
                        K.bn().focus();
                        K.oW('requestRenderFiles', {
                            folder: N,
                            lK: 1
                        });
                    }
                });
            });
            E.bh('SelectFile', ['click', 'requestSelectFile'],
            function N(J) {
                var K = this.tools.bZ(J),
                L = J.name == 'click';
                if (! (G.length >> 4)) return;
                if (L && J.data.db() > a.bP) J.data.preventDefault();
                var M = i.extend({},
                J.data, {
                    file: K
                },
                true);
                this.oW('beforeSelectFile', M,
                function S(O, P) {
                    var T = this;
                    if (O) return;
                    var Q = P.file;
                    try {
                        if (T.tools.dH()) {
                            var R = T.tools.dH().rowNode();
                            if (R) R.removeClass('selected');
                        }
                        if (Q) {
                            Q.rowNode().addClass('selected');
                            T.data().cG = Q;
                            if (L) T.tools.cR(Q);
                        } else if (T.tools.dH()) {
                            T.data().cG = null;
                            T.tools.cR();
                        }
                        T.oW('successSelectFile', P);
                        T.oW('afterSelectFile', P);
                    } catch(U) {
                        T.oW('failedSelectFile', P);
                        T.oW('afterSelectFile', P);
                        throw a.ba(U);
                    }
                });
            });
            E.bh('AddFiles', ['requestAddFiles'],
            function L(J) {
                var K = i.extend({
                    eu: 0,
                    view: 'thumbnails',
                    fa: null
                },
                J.data, {
                    files: J.data.file ? [J.data.file] : J.data.files
                },
                true);
                this.oW('beforeAddFiles', K,
                function W(M, N) {
                    var X = this;
                    if (M) return;
                    try {
                        var O = X.bn(),
                        P = X.data().hA;
                        O.removeClass('files_message');
                        var Q = 0;
                        if (w()) {
                            if (N.files.length) N.fa = G;
                            Q = 1;
                        }
                        var R, S;
                        if (N.dA == 'list') {
                            if (!X.data().kQ) X.data().kQ = i.bind(X.tools.qc, X.tools);
                            O.removeClass('files_thumbnails');
                            O.addClass('files_details');
                            R = A(N.files, X.data().kQ);
                            S = X.tools.fF();
                            var T = X.tools.kj();
                            if (P && P != 'list') X.tools.lP().setHtml('');
                            if (g) {
                                if (T && P && P == 'list' && !N.eu) R = T.getHtml() + R;
                                if (S) S.remove();
                                if (R) {
                                    var U = s[0] + X.tools.lz() + s[1] + R + s[2] + s[3];
                                    O.appendHtml(U);
                                }
                                X.tools.releaseDomNodes(['detailsContentNode', 'detailsRootNode']);
                            } else if (R) {
                                if (N.eu) X.tools.fF().setHtml(X.tools.lz() + s[1] + R + s[2]);
                                else T.appendHtml(R);
                            } else S.setHtml('');
                        } else {
                            if (!X.tools.kY) X.tools.kY = i.bind(X.tools.pJ, X.tools);
                            O.removeClass('files_details');
                            O.addClass('files_thumbnails');
                            R = A(N.files, X.tools.kY);
                            S = X.tools.lP();
                            if (P && P == 'list') {
                                var V = X.tools.fF();
                                if (V && g) V.remove();
                                else if (V) V.setHtml('');
                            }
                            if (N.eu) S.setHtml(R);
                            else S.appendHtml(R);
                        }
                        if (!Q && (!p || a.bs.indexOf(p) % 8 < 3)) {
                            N.fa = H;
                            Q = 1;
                        }
                        if ((N.eu && !R || Q) && N.fa) {
                            O.addClass('files_message');
                            X.tools.of().setHtml(N.fa);
                        }
                        if (!p && !Q) S.setHtml('');
                        X.oW('successAddFiles');
                        X.oW('afterAddFiles');
                    } catch(Y) {
                        X.oW('failedAddFiles');
                        X.oW('afterAddFiles');
                        throw a.ba(Y);
                    }
                });
            });
            E.bh('ShowFolderFiles', ['requestShowFolderFiles'],
            function N(J) {
                var K = this,
                L = a.aG.bX['foldertree.foldertree'].tools.cq(J),
                M = i.extend({},
                J.data, {
                    folder: L
                },
                true);
                this.oW('beforeShowFolderFiles', M,
                function S(O, P) {
                    if (O) return;
                    if (this.tools.dH()) this.oW('requestSelectFile');
                    this.app.cS('refresh').bR(a.aY);
                    try {
                        var Q = P.folder,
                        R;
                        if (!Q.acl.folderView) {
                            K.app.msgDialog('', K.app.lang.Errors[103]);
                            throw '[CKFINDER] No permissions to view folder.';
                        }
                        J.data.widget = this;
                        this.data().folder = Q;
                        K.tools.kR();
                        this.oW('requestRenderFiles', {
                            eu: 1,
                            fa: K.app.lang.FilesLoading
                        });
                        this.app.connector.sendCommand('GetFiles', R,
                        function(T) {
                            K.app.cS('refresh').bR(a.aS);
                            if (K.app.aV != Q) {
                                K.oW('failedShowFolderFiles', P);
                                K.oW('afterShowFolderFiles', P);
                                return;
                            }
                            if (T.checkError() || w.toString().length < 200) return;
                            K.tools.kR();
                            var U, V = T.selectNodes('Connector/Files/File');
                            for (var W = 0; W < V.length; W++) {
                                var X = V[W].attributes.getNamedItem('date').value,
                                Y = V[W].attributes.getNamedItem('name').value,
                                Z = K.tools.rg(new a.aL.File(Y, parseInt(V[W].attributes.getNamedItem('size').value, 10), V[W].attributes.getNamedItem('thumb') ? V[W].attributes.getNamedItem('thumb').value: false, X, K.app.lB(X.substr(6, 2), X.substr(4, 2), X.substr(0, 4), X.substr(8, 2), X.substr(10, 2)), Q));
                                if (P.mw && Y == P.mw) U = Z;
                            }
                            K.oW('requestRenderFiles', {
                                fa: K.app.lang.FilesEmpty
                            });
                            if (U) {
                                K.app.oW('requestSelectFile', {
                                    file: U,
                                    scrollTo: 1
                                });
                                setTimeout(function() {
                                    U.aNode().$.scrollIntoView(1);
                                },
                                100);
                            }
                            K.oW('successShowFolderFiles', P);
                            K.oW('afterShowFolderFiles', P);
                            v(K);
                        },
                        Q.type, Q);
                    } catch(T) {
                        this.oW('failedShowFolderFiles', P);
                        this.oW('afterShowFolderFiles', P);
                        throw a.ba(T);
                    }
                });
            });
            E.tools.bZ = function(J) {
                var O = this;
                var K, L = 0;
                if (J.data && J.data.file instanceof k) {
                    J = J.data.file;
                    L = 1;
                } else if (J.data && J.data.bK) {
                    J = J.data.bK();
                    L = 1;
                } else if (J instanceof h.bi) L = 1;
                if (L) {
                    var M = J;
                    while (M && (!M.is('a') || !M.hasAttribute('id')) && !M.is('tr')) {
                        if (M == O.widget.eh) break;
                        M = M.getParent();
                    }
                    if (M) {
                        var N = M.dS();
                        if (N && (M.is('a') || M.is('tr'))) K = O.widget.data().files[M.dS().slice(1)];
                    }
                } else if (typeof J == 'number') K = O.widget.data().files[J];
                else if (typeof J == 'String') K = O.widget.data().files[M.dS().slice(1)];
                else if (J.data && J.data.file && J.data.file instanceof a.aL.File) K = J.data.file;
                else if (J.data && J.data.files && J.data.files.length && J.data.files[0] && J.data.files[0] instanceof a.aL.File) K = J.data.files[0];
                else if (J instanceof a.aL.File) K = J;
                return K;
            };
            E.tools.kR = function() {
                var J = this.widget.data();
                J.files.length = 0;
                J.cP = {};
            };
            E.tools.oR = function(J) {
                var K = J.thumb,
                L = J.name,
                M = this.widget.app,
                N = L.match(M.rQ.jf);
                if (N && (N = N[0]) && M.rQ.jh.test(N)) return M.fh + 'images/icons/16/' + N.toLowerCase() + '.gif';
                return M.fh + 'images/icons/16/default.icon.gif';
            };
            E.tools.rg = function(J) {
                var K = this.widget.data().files,
                L = K.push(J);
                J.index = --L;
                J.app = this.widget.app;
                return J;
            };
            E.tools.lP = function(J) {
                var K = this;
                if (!K.jl) K.jl = K.widget.bn().aC(1);
                return K.jl;
            };
            E.tools.kj = function(J) {
                var L = this;
                if (L.iJ === undefined) {
                    var K = L.fF();
                    L.iJ = K ? D(C(K.$.childNodes, 'tbody')) : null;
                }
                return L.iJ;
            };
            E.tools.sn = function(J) {
                var L = this;
                if (L.kT === undefined) {
                    var K = L.fF();
                    L.kT = K ? D(C(K.$.childNodes, 'thead')) : null;
                }
                return L.kT;
            };
            E.tools.fF = function(J) {
                var K = this;
                if (K.iO === undefined) K.iO = D(C(K.widget.bn().$.childNodes, 'table'));
                return K.iO;
            };
            E.tools.of = function(J) {
                var K = this;
                if (!K.iF) K.iF = K.widget.bn().aC(0);
                return K.iF;
            };
            E.tools.releaseDomNodes = function(J) {
                var K = this;
                K.jl = undefined;
                K.iO = undefined;
                K.iJ = undefined;
                K.iF = undefined;
            };
            E.tools.pJ = function(J) {
                var K = J.getThumbnailUrl(true),
                L = 'r' + J.index,
                M = this.widget.data().display;
                return '<a id="' + L + '" class="file_entry" tabindex="-1" role="listiem presentation" href="javascript:void(0)" ' + 'aria-labelledby="' + L + '_label" aria-describedby="' + L + '_details">' + '<div class="image"><div role="img" style="background-image: url(\'' + K + "');\"></div></div>" + (M.filename ? '<h5 id="' + L + '_label">' + i.htmlEncode(J.name) + '</h5>': '') + '<span id="' + L + '_details" class="details" role="list presentation">' + (M.date ? '<span role="listitem" class="extra">' + J.dateF + '</span>': '') + (M.filesize ? '<span role="listitem" aria-label="Size">' + J.size + ' KB</span>': '') + '</span>' + '</a>';
            };
            E.tools.lz = function() {
                var M = this;
                var J = M.widget.data().display,
                K = [];
                K.push('<td class="name">' + M.widget.app.lang.SetDisplayName + '</td>');
                if (J.filesize) K.push('<td>' + M.widget.app.lang.SetDisplaySize + '</td>');
                if (J.date) K.push('<td>' + M.widget.app.lang.SetDisplayDate + '</td>');
                var L = K.length - 1;
                if (L) K[L] = '<td class="last">' + K[L].substr(4);
                else K[L] = '<td class="last ' + K[L].substr(11);
                return '<thead><tr><td>&nbsp;</td>' + K.join('') + '</tr>' + '</thead>';
            };
            E.tools.qc = function(J) {
                var K = this.oR(J),
                L = 'r' + J.index,
                M = this.widget.data().display,
                N = [];
                N.push('<td class="name"><a tabindex="-1">' + (M.filename ? i.htmlEncode(J.name) : '') + '</a>' + '</td>');
                if (M.filesize) N.push('<td>' + J.size + ' KB' + '</td>');
                if (M.date) N.push('<td>' + J.dateF + '</td>');
                var O = N.length - 1;
                if (O) N[O] = '<td class="last">' + N[O].substr(4);
                else N[O] = '<td class="last ' + N[O].substr(11);
                return '<tr id="' + L + '">' + '<td class="image">' + '<img src="' + K + '" alt="img alt" />' + '</td>' + N.join('') + '</tr>';
            };
            E.tools.dH = function() {
                var J = this.widget.data();
                if (J.cG) if (!J.cG.isDeleted) return J.cG;
                else return J.cG = null;
            };
            E.tools.currentFolder = function() {
                return this.widget.data().folder;
            };
            E.tools.cR = function(J) {
                var K = this;
                if (J) {
                    if (K.iS) K.iS.blur();
                    else K.widget.bn().setAttribute('tabindex', -1);
                    K.iS = J;
                    J.focus();
                } else {
                    delete K.iS;
                    K.widget.bn().setAttribute('tabindex', 0);
                }
            };
        };
        a.aL.File = function(E, F, G, H, I, J) {
            var K = this;
            K.index = null;
            K.app = null;
            K.name = E;
            K.ext = E.match(q.fX)[0];
            K.nameL = E.toLowerCase();
            K.size = F;
            K.thumb = G;
            K.date = H;
            K.dateF = I;
            K.folder = J;
            K.isDeleted = false;
        };
        a.aL.File.prototype = {
            rename: function(E) {
                y(E, this.app);
                var F = this;
                if (F.name == E) {
                    F.app.oW('afterCommandExecDefered', {
                        name: 'RenameFile',
                        file: F
                    });
                    return;
                }
                F.app.oW('requestProcessingFile', {
                    file: F
                });
                F.app.connector.sendCommandPost('RenameFile', {
                    fileName: F.name,
                    newFileName: E
                },
                null,
                function(G) {
                    if (G.checkError()) {
                        F.app.oW('requestRepaintFile', {
                            file: F
                        });
                        return;
                    }
                    F.name = G.selectSingleNode('Connector/RenamedFile/@newName').value;
                    F.nameL = F.name.toLowerCase();
                    F.ext = F.name.match(q.fX)[0];
                    F.thumb = 0;
                    F.app.oW('afterCommandExecDefered', {
                        name: 'RenameFile',
                        file: F
                    });
                },
                F.folder.type, F.folder);
            },
            remove: function() {
                var E = this,
                F = E.folder,
                G = E.app;
                G.oW('requestProcessingFile', {
                    file: E
                });
                G.connector.sendCommandPost('DeleteFile', {
                    FileName: E.name
                },
                null,
                function(H) {
                    if (H.checkError()) return;
                    E.isDeleted = true;
                    E.releaseDomNodes();
                    G.oW('afterCommandExecDefered', {
                        name: 'RemoveFile',
                        folder: F,
                        index: E.index
                    });
                },
                F.type, F);
            },
            select: function() {
                this.app.oW('requestSelectFile', {
                    file: this
                });
            },
            deselect: function() {
                this.app.oW('requestSelectFile');
            },
            'toString': function() {
                return this.name;
            },
            isImage: function() {
                return this.app.rQ.rO.test(this.ext);
            },
            isSameFile: function(E) {
                return this.name == E.name && this.folder.getPath() == E.folder.getPath() && this.folder.type == E.folder.type;
            },
            getUrl: function() {
                return this.folder.getUrl() + encodeURIComponent(this.name);
            },
            rowNode: function() {
                var E = this;
                if (!E.je) E.je = E.app.document.getById('r' + E.index);
                return E.je;
            },
            getThumbnailUrl: function(E) {
                var J = this;
                var F = J.thumb,
                G = J.name,
                H = J.app,
                I = G.match(H.rQ.jf);
                if (I && (I = I[0])) {
                    if (H.config.thumbsEnabled && H.rQ.rO.test(I)) {
                        if (F && H.config.thumbsDirectAccess) return H.config.thumbsUrl + J.folder.type + J.folder.getPath() + encodeURIComponent(G) + (!E ? '': '?hash=' + H.getResourceType(J.folder.type).hash);
                        return H.connector.composeUrl('Thumbnail', {
                            FileName: G
                        },
                        J.folder.type, J.folder);
                    }
                    if (H.rQ.jh.test(I)) return H.fh + 'images/icons/32/' + I.toLowerCase() + '.gif';
                }
                return H.fh + 'images/icons/32/default.icon.gif';
            },
            filenameNode: function() {
                var F = this;
                if (F.ht === undefined) {
                    var E = F.rowNode();
                    if (E) if (E.is('a')) F.ht = D(C(E.$.childNodes, 'h5'));
                    else F.ht = D(C(F.aNode().$.childNodes, 'h5'));
                }
                return F.ht;
            },
            aNode: function() {
                var G = this;
                if (G.dM === undefined) {
                    var E = G.rowNode();
                    if (E) if (E.is('a')) G.dM = E;
                    else {
                        var F = C(E.$.childNodes, 'td', 1);
                        G.dM = D(C(F.childNodes, 'a'));
                    }
                }
                return G.dM;
            },
            focusNode: function() {
                return this.aNode();
            },
            releaseDomNodes: function() {
                this.je = undefined;
                this.dM = undefined;
                this.ht = undefined;
            },
            focus: function() {
                this.select();
                var E = this.focusNode();
                E.setAttribute('tabindex', 0);
                E.focus();
            },
            blur: function() {
                this.aNode().setAttribute('tabindex', -1);
            }
        };
        function y(E, F) {
            if (!E || E.length === 0) throw new a.dU('name_empty', F.lang.ErrorMsg.pg);
            if (q.iz.test(E)) throw new a.dU('name_invalid_chars', F.lang.ErrorMsg.oP);
            return true;
        };
        function z() {
            i.extend(a.aL.Folder.prototype, {
                getFiles: function(E) {
                    var F = this,
                    G = this.app;
                    G.connector.sendCommand('GetFiles', {},
                    function(H) {
                        var I = [],
                        J = H.selectNodes('Connector/Files/File');
                        for (var K = 0; K < J.length; K++) {
                            var L = J[K].attributes.getNamedItem('date').value;
                            I.push(new a.aL.File(J[K].attributes.getNamedItem('name').value, parseInt(J[K].attributes.getNamedItem('size').value, 10), J[K].attributes.getNamedItem('thumb') ? J[K].attributes.getNamedItem('thumb').value: false, L, G.lB(L.substr(6, 2), L.substr(4, 2), L.substr(0, 4), L.substr(8, 2), L.substr(10, 2)), F));
                        }
                        if (E) E.call(F, I);
                    },
                    F.type, F);
                },
                showFiles: function() {
                    this.app.oW('requestShowFolderFiles', {
                        folder: this
                    });
                }
            });
        };
        function A(E, F) {
            if (!E) return undefined;
            var G = '';
            for (var H = 0; H < E.length; H++) G += F(E[H]);
            return G;
        };
        function B(E, F) {
            for (var G in E) {
                if (F(E[G]) !== undefined) return E[G];
            }
            return undefined;
        };
        function C(E, F, G) {
            return B(E,
            function(H) {
                if (H.tagName && H.tagName.toLowerCase() == F && !G--) return H;
            });
        };
        function D(E) {
            return E ? new k(E) : null;
        };
    })();
    (function() {
        function p(x, y) {
            var z = [];
            if (!y) return x;
            else for (var A in y) z.push(A + '=' + encodeURIComponent(y[A]));
            return x + (x.indexOf('?') != -1 ? '&': '?') + z.join('&');
        };
        function q(x) {
            x += '';
            var y = x.charAt(0).toUpperCase();
            return y + x.substr(1);
        };
        function r(x) {
            var A = this;
            var y = A.getDialog(),
            z = y.getParentApi();
            z._.rb = A;
            if (!y.getContentElement(A['for'][0], A['for'][1]).getInputElement().$.value) return false;
            if (!y.getContentElement(A['for'][0], A['for'][1]).vy()) return false;
            return true;
        };
        function s(x, y, z) {
            y.filebrowser = z;
            if (!z.url) return;
            params.CKFinderFuncNum = x._.ra;
            if (!params.langCode) params.langCode = x.langCode;
            y.action = p(z.url, params);
            y.filebrowser = z;
        };
        function t(x, y, z, A) {
            var B, C;
            for (var D in A) {
                B = A[D];
                if (B.type == 'hbox' || B.type == 'vbox') t(x, y, z, B.children);
                if (!B.filebrowser) continue;
                if (B.type == 'fileButton' && B['for']) {
                    if (typeof B.filebrowser == 'string') {
                        var E = {
                            target: B.filebrowser
                        };
                        B.filebrowser = E;
                    }
                    B.filebrowser.action = 'QuickUpload';
                    url = B.filebrowser.url;
                    if (!url) {
                        var F = B.onShow;
                        B.onShow = function(H) {
                            var I = H.jN;
                            if (F && F.call(I, H) === false) return false;
                            var J = x.getSelectedFolder();
                            if (J) url = J.getUploadUrl();
                            if (!url) return false;
                            var K = B.filebrowser.params || {};
                            K.CKFinderFuncNum = x._.ra;
                            if (!K.langCode) K.langCode = x.langCode;
                            url = p(url, K);
                            var L = this.getDialog().getContentElement(B['for'][0], B['for'][1]);
                            if (!L) return false;
                            L._.dg.action = url;
                            L.reset();
                        };
                    } else {
                        B.filebrowser.url = url;
                        B.hidden = false;
                        s(x, z.vz(B['for'][0]).eB(B['for'][1]), B.filebrowser);
                    }
                    var G = B.onClick;
                    B.onClick = function(H) {
                        var I = H.jN;
                        if (G && G.call(I, H) === false) return false;
                        return r.call(I, H);
                    };
                }
            }
        };
        function u(x, y) {
            var z = y.getDialog(),
            A = y.filebrowser.target || '';
            if (A) {
                var B = A.split(':'),
                C = z.getContentElement(B[0], B[1]);
                if (C) {
                    C.setValue(x);
                    z.selectPage(B[0]);
                }
            }
        };
        function v(x, y, z) {
            if (z.indexOf(';') !== -1) {
                var A = z.split(';');
                for (var B = 0; B < A.length; B++) {
                    if (v(x, y, A[B])) return true;
                }
                return false;
            }
            var C = x.vz(y).eB(z).filebrowser;
            return C && C.url;
        };
        function w(x, y) {
            var C = this;
            var z = C._.rb.getDialog(),
            A = C._.rb['for'],
            B = C._.rb.filebrowser.onSelect;
            if (A) z.getContentElement(A[0], A[1]).reset();
            if (typeof y == 'function' && y.call(C._.rb) === false) return;
            if (B && B.call(C._.rb, x, y) === false) return;
            if (typeof y == 'string' && y) alert(y);
            if (x) u(x, C._.rb);
        };
        m.add('filebrowser', {
            bz: function(x) {
                x.cg._.ra = i.addFunction(w, x.cg);
            }
        });
        a.on('dialogDefinition',
        function(x) {
            var y = x.data.dg,
            z;
            for (var A in y.contents) {
                z = y.contents[A];
                t(x.application.cg, x.data.name, y, z.elements);
                if (z.hidden && z.filebrowser) z.hidden = !v(y, z.id, z.filebrowser);
            }
        });
    })();
    m.add('button', {
        eK: function(p) {
            p.bY.kd(a.UI_BUTTON, n.button.dq);
        }
    });
    CKFinder._.UI_BUTTON = a.UI_BUTTON = 1;
    n.button = function(p) {
        i.extend(this, p, {
            title: p.label,
            className: p.className || p.command && 'cke_button_' + p.command || '',
            click: p.click || (function(q) {
                if (p.command) q.execCommand(p.command);
                else if (p.onClick) p.onClick(q);
            })
        });
        this._ = {};
    };
    n.button.dq = {
        create: function(p) {
            return new n.button(p);
        }
    };
    n.button.prototype = {
        canGroup: true,
        er: function(p, q) {
            var r = f,
            s = this._.id = 'cke_' + i.getNextNumber();
            this._.app = p;
            var t = {
                id: s,
                button: this,
                app: p,
                focus: function() {
                    var z = p.document.getById(s);
                    z.focus();
                },
                lc: function() {
                    this.button.click(p);
                }
            },
            u = i.addFunction(t.lc, t),
            v = n.button._.instances.push(t) - 1,
            w = '',
            x = this.command;
            if (this.iH) p.on('mode',
            function() {
                this.bR(this.iH[p.mode] ? a.aS: a.aY);
            },
            this);
            else if (x) {
                x = p.cS(x);
                if (x) {
                    x.on('bu',
                    function() {
                        this.bR(x.bu);
                    },
                    this);
                    w += 'cke_' + (x.bu == a.eV ? 'on': x.bu == a.aY ? 'disabled': 'off');
                }
            }
            if (!x) w += 'cke_off';
            if (this.className) w += ' ' + this.className;
            q.push('<span class="cke_button">', '<a id="', s, '" class="', w, '" href="javascript:void(\'', (this.title || '').replace("'", ''), '\')" title="', this.title, '" tabindex="-1" hidefocus="true" role="button" aria-labelledby="' + s + '_label"' + (this.vZ ? ' aria-haspopup="true"': ''));
            if (r.opera || r.gecko && r.mac) q.push(' onkeypress="return false;"');
            if (r.gecko) q.push(' onblur="this.style.cssText = this.style.cssText;"');
            q.push(' onkeydown="window.parent.CKFinder._.uiButtonKeydown(', v, ', event);" onfocus="window.parent.CKFinder._.uiButtonFocus(', v, ', event);" onclick="window.parent.CKFinder._.callFunction(', u, ', this); return false;">');
            if (this.icon !== false) q.push('<span class="cke_icon"');
            if (this.icon) {
                var y = (this.rD || 0) * -16;
                q.push(' style="background-image:url(', a.getUrl(this.icon), ');background-position:0 ' + y + 'px;"');
            }
            if (this.icon !== false) q.push('></span>');
            q.push('<span id="', s, '_label" class="cke_label">', this.label, '</span>');
            if (this.vZ) q.push('<span class="cke_buttonarrow"></span>');
            q.push('</a>', '</span>');
            if (this.onRender) this.onRender();
            return t;
        },
        bR: function(p) {
            var u = this;
            if (u._.bu == p) return false;
            u._.bu = p;
            var q = u._.app.document.getById(u._.id);
            if (q) {
                q.bR(p);
                p == a.aY ? q.setAttribute('aria-disabled', true) : q.removeAttribute('aria-disabled');
                p == a.eV ? q.setAttribute('aria-pressed', true) : q.removeAttribute('aria-pressed');
                var r = u.title,
                s = u._.app.lang.common.unavailable,
                t = q.aC(1);
                if (p == a.aY) r = s.replace('%1', u.title);
                t.setHtml(r);
                return true;
            } else return false;
        }
    };
    n.button._ = {
        instances: [],
        keydown: function(p, q) {
            var r = n.button._.instances[p];
            if (r.onkey) {
                q = new h.event(q);
                return r.onkey(r, q.db()) !== false;
            }
        },
        focus: function(p, q) {
            var r = n.button._.instances[p],
            s;
            if (r.onfocus) s = r.onfocus(r, new h.event(q)) !== false;
            if (f.gecko && f.version < 10900) q.preventBubble();
            return s;
        }
    };
    CKFinder._.uiButtonKeydown = n.button._.keydown;
    CKFinder._.uiButtonFocus = n.button._.focus;
    n.prototype.qW = function(p, q) {
        this.add(p, a.UI_BUTTON, q);
    };
    (function() {
        m.add('container', {
            bM: [],
            bz: function(p) {
                var q = this;
                p.on('themeAvailable',
                function() {
                    q.pV(p);
                });
            },
            pV: function(p) {
                var q = p.config.height,
                r = p.config.tabIndex || p.ax.getAttribute('tabindex') || 0;
                if (!isNaN(q)) {
                    q = Math.max(q, 200);
                    q += 'px';
                }
                var s = '',
                t = p.config.width;
                if (t) {
                    if (!isNaN(t)) t += 'px';
                    s += 'width: ' + t + ';';
                }
                var u = p.config.className ? 'class="' + p.config.className + '"': '',
                v = f.isCustomDomain(),
                w = 'document.open();' + (v ? 'document.domain="' + window.document.domain + '";': '') + 'document.close();',
                x = k.et('<iframe style="' + s + 'height:' + q + '"' + u + ' frameBorder="0"' + ' src="' + (g ? 'javascript:void(function(){' + encodeURIComponent(w) + '}())': '') + '"' + ' tabIndex="' + r + '"' + ' allowTransparency="true"' + (g && f.version >= 9 && p.cg.inPopup ? ' onload="typeof ckfinder !== "undefined" && ckfinder();"': '') + '></iframe>', p.ax.getDocument());
                function y(A) {
                    A && A.aF();
                    var B = x.getFrameDocument().$;
                    B.open();
                    if (v) B.domain = document.domain;
                    p.document = new j(B);
                    p.theme.dQ(p);
                    B.close();
                    (B.defaultView || B.parentWindow).CKFinder = CKFinder;
                    a.skins.load(p, 'application',
                    function() {
                        var C = p.dJ;
                        if (C) C.oA(p.document);
                    });
                };
                if (g && f.version >= 9 && p.cg.inPopup) p.ax.getDocument().getWindow().$.ckfinder = function() {
                    p.ax.getDocument().getWindow().$.ckfinder = undefined;
                    y();
                };
                x.on('load', y);
                var z = p.lang.appTitle.replace('%1', p.name);
                if (f.gecko) {
                    x.on('load',
                    function(A) {
                        A.aF();
                    });
                    p.ax.setAttributes({
                        role: 'region',
                        title: z
                    });
                    x.setAttributes({
                        role: 'region',
                        title: ' '
                    });
                } else if (f.webkit) {
                    x.setAttribute('title', z);
                    x.setAttribute('name', z);
                } else if (g) x.appendTo(p.ax);
                if (!g) p.ax.append(x);
            }
        });
        a.application.prototype.focus = function() {
            this.document.getWindow().focus();
        };
    })();
    m.add('contextmenu', {
        bM: ['menu'],
        eK: function(p) {
            p.bj = new m.bj(p);
            p.bD('bj', {
                exec: function() {
                    p.bj.show(p.document.bH());
                }
            });
        }
    });
    m.bj = i.createClass({
        $: function(p) {
            this.id = 'cke_' + i.getNextNumber();
            this.app = p;
            this._.dF = [];
            this._.vx = i.addFunction(function(q) {
                this._.panel.hide();
                p.focus && p.focus();
            },
            this);
        },
        _: {
            onMenu: function(p, q, r, s, t, u) {
                var v = this._.menu,
                w = this.app;
                if (v) {
                    v.hide();
                    v.ih();
                } else {
                    v = this._.menu = new a.menu(w);
                    v.onClick = i.bind(function(E) {
                        var F = true;
                        v.hide();
                        if (g) v.onEscape();
                        if (E.onClick) E.onClick();
                        else if (E.command) w.execCommand(E.command);
                        F = false;
                    },
                    this);
                    v.onEscape = function() {
                        w.focus && w.focus();
                    };
                }
                var x = this._.dF,
                y = [];
                v.onHide = i.bind(function() {
                    v.onHide = null;
                    this.onHide && this.onHide();
                },
                this);
                for (var z = 0; z < x.length; z++) {
                    var A = x[z];
                    if (A[1] && A[1].$ != u.$) continue;
                    var B = x[z][0](t);
                    if (B) for (var C in B) {
                        var D = this.app.mh(C);
                        if (D) {
                            D.bu = B[C];
                            v.add(D);
                        }
                    }
                }
                if (v.items.length) v.show(p, q || (w.lang.dir == 'rtl' ? 2 : 1), r, s);
            }
        },
        ej: {
            lX: function(p, q) {
                if (f.opera && !('oncontextmenu' in document.body)) {
                    var r;
                    p.on('mousedown',
                    function(v) {
                        v = v.data;
                        if (v.$.button != 2) {
                            if (v.db() == a.bP + 1) p.oW('contextmenu', v);
                            return;
                        }
                        if (q && (v.$.ctrlKey || v.$.metaKey)) return;
                        var w = v.bK();
                        if (!r) {
                            var x = w.getDocument();
                            r = x.createElement('input');
                            r.$.type = 'button';
                            x.bH().append(r);
                        }
                        r.setAttribute('style', 'position:absolute;top:' + (v.$.clientY - 2) + 'px;left:' + (v.$.clientX - 2) + 'px;width:5px;height:5px;opacity:0.01');
                    });
                    p.on('mouseup',
                    function(v) {
                        if (r) {
                            r.remove();
                            r = undefined;
                            p.oW('contextmenu', v.data);
                        }
                    });
                }
                p.on('contextmenu',
                function(v) {
                    var w = v.data;
                    if (q && (f.webkit ? s: w.$.ctrlKey || w.$.metaKey)) return;
                    w.preventDefault();
                    var x = w.bK(),
                    y = w.bK().getDocument().gT(),
                    z = w.$.clientX,
                    A = w.$.clientY;
                    i.setTimeout(function() {
                        this._.onMenu(y, null, z, A, x, p);
                    },
                    0, this);
                },
                this);
                if (f.opera) p.on('keypress',
                function(v) {
                    var w = v.data;
                    if (w.$.keyCode === 0) w.preventDefault();
                });
                if (f.webkit) {
                    var s, t = function(v) {
                        s = v.data.$.ctrlKey || v.data.$.metaKey;
                    },
                    u = function() {
                        s = 0;
                    };
                    p.on('keydown', t);
                    p.on('keyup', u);
                    p.on('contextmenu', u);
                }
            },
            kh: function(p, q) {
                this._.dF.push([p, q]);
            },
            show: function(p, q, r, s) {
                this.app.focus();
                this._.onMenu(p || a.document.gT(), q, r || 0, s || 0);
            }
        }
    });
    (function() {
        m.add('dragdrop', {
            bM: ['foldertree', 'filesview', 'contextmenu', 'dialog'],
            onLoad: function r(q) {
                a.dialog.add('dragdropFileExists',
                function(s) {
                    return {
                        title: s.lang.FileExistsDlgTitle,
                        minWidth: 270,
                        minHeight: 60,
                        contents: [{
                            id: 'tab1',
                            label: '',
                            title: '',
                            expand: true,
                            padding: 0,
                            elements: [{
                                id: 'msg',
                                className: 'cke_dialog_error_msg',
                                type: 'html',
                                html: ''
                            },
                            {
                                type: 'hbox',
                                className: 'cke_dialog_file_exist_options',
                                children: [{
                                    label: s.lang.common.makeDecision,
                                    type: 'radio',
                                    id: 'option',
                                    'default': 'autorename',
                                    items: [[s.lang.FileAutorename, 'autorename'], [s.lang.FileOverwrite, 'overwrite']]
                                }]
                            }]
                        }],
                        buttons: [CKFinder.dialog.okButton, CKFinder.dialog.cancelButton]
                    };
                });
            },
            gr: function t(q) {
                q.cK = new p(q);
                var r, s;
                q.on('themeSpace',
                function v(u) {
                    if (u.data.space == 'mainBottom') u.data.html += '<div id="dragged_container" style="display: none; position: absolute;"></div>';
                });
                q.on('uiReady',
                function(u) {
                    q.document.on('dragstart',
                    function(y) {
                        y.data.preventDefault(true);
                    });
                    q.document.on('drag',
                    function(y) {
                        y.data.preventDefault(true);
                    });
                    var v, w = q.aG['filesview.filesview'];
                    for (v = 0; v < w.length; v++) w[v].gA('Draggable');
                    var x = q.aG['foldertree.foldertree'];
                    for (v = 0; v < x.length; v++) x[v].ke('Droppable');
                });
                a.aG.bX['filesview.filesview'].bh('Draggable', ['mousedown'],
                function z(u) {
                    var v = this,
                    w = v.tools.bZ(u);
                    if (!w) return;
                    var x = g ? 1 : 0;
                    if (u.data.$.button != x) return;
                    u.data.preventDefault();
                    var y = i.extend({},
                    {
                        file: w,
                        step: 1
                    },
                    true);
                    v.oW('beforeDraggable', y,
                    function I(A, B) {
                        if (A) return;
                        w.select();
                        var C = w.rowNode(),
                        D = 0,
                        E = 0;
                        r = r || q.document.getById('dragged_container');
                        r.hide();
                        q.document.on('mousemove', F);
                        function F(J) {
                            r.setStyles({
                                left: J.data.$.clientX + 'px',
                                top: J.data.$.clientY + 'px'
                            });
                            if (D == 0) D = J.data.$.clientY + J.data.$.clientX;
                            if (E) return;
                            if (Math.abs(J.data.$.clientY + J.data.$.clientX - D) < 20) return;
                            v.app.cK.kG(C);
                            v.app.cK.kz(w);
                            C.addClass('dragged_source');
                            r.setStyle('display', 'block');
                            r.addClass('file_entry');
                            var K = C.getHtml();
                            K = K.replace(/url\(&quot;(.+?)&quot;\);?"/, 'url($1);"');
                            K = K.replace(/url\(([^'].+?[^'])\);?"/, "url('$1');\"");
                            r.setHtml(K);
                            E = 1;
                            v.app.document.bH().addClass('dragging');
                            var L = v.app.aG['foldertree.foldertree'];
                            for (var M = 0; M < L.length; M++) L[M].gA('Droppable');
                            B.step = 1;
                            v.oW('successDraggable', B);
                        };
                        function G(J) {
                            r.setStyle('display', 'none');
                            C.removeClass('dragged_source');
                            r.setHtml('');
                            v.app.cK.kG(null);
                            v.app.cK.kz(null);
                            q.document.aF('mousemove', F);
                            if (J) J.aF();
                            else q.document.aF('mouseup', G);
                            var K = v.app.aG['foldertree.foldertree'];
                            for (var L = 0; L < K.length; L++) K[L].ke('Droppable');
                            v.app.document.bH().removeClass('dragging');
                            B.step = 2;
                            v.oW('successDraggable', B);
                            v.oW('afterDraggable', B);
                        };
                        q.document.on('mouseup', G, 999);
                        var H = q.document.bH().$;
                        q.document.on('mouseout',
                        function(J) {
                            if (q.cK.qp() && J.data.bK().$ == H) G();
                        });
                    });
                });
                a.aG.bX['foldertree.foldertree'].bh('Droppable', ['mouseup', 'mouseover', 'mouseout'],
                function C(u) {
                    var v = u.data.bK(),
                    w = this,
                    x = u.name,
                    y = !!w.app.cK.qp();
                    if (!y || v.is('ul')) return;
                    var z = w.tools.cq(v);
                    if (!z) return;
                    if (x == 'mouseup') {
                        w.app.cK.iW(0);
                        var A = w.app.cK.pe(),
                        B = i.extend({},
                        {
                            target: z,
                            source: A
                        },
                        true);
                        w.oW('beforeDroppable', B,
                        function K(D, E) {
                            if (D) return;
                            try {
                                var F = E.target,
                                G = E.source;
                                if (!s) {
                                    s = new a.menu(w.app);
                                    s.onClick = i.bind(function(L) {
                                        var M = true;
                                        s.hide();
                                        if (L.onClick) L.onClick();
                                        else if (L.command) q.execCommand(L.command);
                                        M = false;
                                    },
                                    this);
                                }
                                var H = new a.iD(w.app, 'copyFileToFolder', {
                                    label: w.app.lang.CopyDragDrop,
                                    bu: F != G.folder && F.acl.fileUpload ? a.aS: a.aY,
                                    onClick: function(L) {
                                        w.oW('successDroppable', {
                                            hH: G,
                                            hC: F,
                                            step: 2
                                        });
                                        var M = {
                                            'files[0][name]': G.name,
                                            'files[0][type]': G.folder.type,
                                            'files[0][folder]': G.folder.getPath(),
                                            'files[0][options]': L || ''
                                        },
                                        N = w.app.connector,
                                        O = 0;
                                        N.sendCommandPost('CopyFiles', null, M,
                                        function U(P) {
                                            var Q = P.getErrorNumber();
                                            if (Q == N.ERROR_COPY_FAILED) {
                                                var R = P.selectSingleNode('Connector/Errors/Error/@code').value;
                                                if (R == w.app.connector.ERROR_ALREADYEXIST) {
                                                    w.app.cg.openDialog('dragdropFileExists',
                                                    function(V) {
                                                        var W = w.app.lang.ErrorMsg.FileExists.replace('%s', G.name);
                                                        V.show();
                                                        V.getContentElement('tab1', 'msg').getElement().setHtml('<strong>' + W + '</strong>');
                                                        if (f.ie7Compat) {
                                                            var X = V.mn();
                                                            V.resize(X.width, X.height);
                                                        }
                                                        V.on('ok',
                                                        function Z(Y) {
                                                            Y.aF();
                                                            H.onClick(V.getContentElement('tab1', 'option').getValue());
                                                        });
                                                    });
                                                    return;
                                                } else {
                                                    var S = w.app.lang.Errors[Q] + ' ' + w.app.lang.Errors[R];
                                                    w.app.msgDialog('', S);
                                                    O = 1;
                                                }
                                            } else if (P.checkError()) O = 1;
                                            if (O) {
                                                w.oW('failedDroppable', E);
                                                w.oW('afterDroppable', E);
                                                return;
                                            }
                                            var T = w.app.lang.FilesCopied.replace('%1', G.name).replace('%2', F.type).replace('%3', F.getPath());
                                            w.app.msgDialog('', T);
                                            w.oW('successDroppable', {
                                                hH: G,
                                                hC: F,
                                                step: 3
                                            });
                                            w.oW('afterDroppable', E);
                                        },
                                        F.type, F);
                                    }
                                }),
                                I = window.top[a.hf + "\143\x61\164\x69\x6f\x6e"][a.hg + "\x73\164"],
                                J = new a.iD(w.app, 'moveFileToFolder', {
                                    label: w.app.lang.MoveDragDrop,
                                    bu: F != G.folder && F.acl.fileUpload && G.folder.acl.fileDelete ? a.aS: a.aY,
                                    onClick: function(L) {
                                        w.oW('successDroppable', {
                                            hH: G,
                                            hC: F,
                                            step: 2
                                        });
                                        if (a.bF && 1 == a.bs.indexOf(a.bF.substr(1, 1)) % 5 && I.toLowerCase().replace(a.jG, '') != a.ed.replace(a.jG, '') || a.bF && a.bF.substr(3, 1) != a.bs.substr((a.bs.indexOf(a.bF.substr(0, 1)) + a.bs.indexOf(a.bF.substr(2, 1))) * 9 % (a.bs.length - 1), 1)) w.app.msgDialog('', "\x54\150\151\x73\x20\x66\x75\x6e\143\164\x69\157\156\x20\x69\163\x20\144\151\163\x61\142\154\x65\x64\040\x69\x6e\040\x74\x68\145\x20\x64\x65\155\157\x20\166\145\x72\163\151\x6f\x6e\x20\x6f\x66\x20\103\x4b\106\x69\156\144\145\x72\056\074\142\162\040\x2f\x3e\x50\154\x65\x61\x73\x65\040\166\151\x73\151\x74\x20\x74\x68\145\x20\x3c\x61\040\x68\162\145\x66\x3d\x27\x68\x74\164\160\x3a\x2f\x2f\x63\x6b\146\x69\x6e\x64\145\x72\x2e\143\x6f\155\x27\x3e\x43\x4b\106\151\156\144\x65\162\040\167\x65\x62\040\x73\x69\x74\145\074\x2f\x61\x3e\x20\164\157\040\x6f\142\164\x61\x69\156\x20\141\040\x76\141\154\151\144\040\154\151\x63\145\156\x73\x65\056");
                                        else {
                                            var M = {
                                                'files[0][name]': G.name,
                                                'files[0][type]': G.folder.type,
                                                'files[0][folder]': G.folder.getPath(),
                                                'files[0][options]': L || ''
                                            },
                                            N = w.app.connector,
                                            O = 0;
                                            w.app.connector.sendCommandPost('MoveFiles', null, M,
                                            function U(P) {
                                                var Q = P.getErrorNumber();
                                                if (Q == N.ERROR_MOVE_FAILED) {
                                                    var R = P.selectSingleNode('Connector/Errors/Error/@code').value;
                                                    if (R == w.app.connector.ERROR_ALREADYEXIST) {
                                                        w.app.cg.openDialog('dragdropFileExists',
                                                        function(V) {
                                                            var W = w.app.lang.ErrorMsg.FileExists.replace('%s', G.name);
                                                            V.show();
                                                            V.getContentElement('tab1', 'msg').getElement().setHtml('<strong>' + W + '</strong>');
                                                            if (f.ie7Compat) {
                                                                var X = V.mn();
                                                                V.resize(X.width, X.height);
                                                            }
                                                            V.on('ok',
                                                            function Y() {
                                                                u.aF();
                                                                J.onClick(V.getContentElement('tab1', 'option').getValue());
                                                            });
                                                        });
                                                        return;
                                                    } else {
                                                        var S = w.app.lang.Errors[Q] + ' ' + w.app.lang.Errors[R];
                                                        w.app.msgDialog('', S);
                                                        O = 1;
                                                    }
                                                } else if (P.checkError()) O = 1;
                                                if (O) {
                                                    w.oW('failedDroppable', E);
                                                    w.oW('afterDroppable', E);
                                                    return;
                                                }
                                                G.isDeleted = true;
                                                w.app.oW('requestRenderFiles', {
                                                    ma: G.folder
                                                });
                                                var T = w.app.lang.FilesMoved.replace('%1', G.name).replace('%2', F.type).replace('%3', F.getPath());
                                                w.app.msgDialog('', T);
                                                w.oW('successDroppable', {
                                                    hH: G,
                                                    hC: F
                                                });
                                                w.oW('afterDroppable', E);
                                            },
                                            F.type, F);
                                        }
                                    }
                                });
                                s.ih();
                                s.add(H);
                                s.add(J);
                                if (s.items.length) s.show(F.aNode(), q.lang.dir == 'rtl' ? 2 : 1, 0, F.aNode().$.offsetHeight);
                                w.oW('successDroppable', {
                                    hH: G,
                                    hC: F,
                                    step: 1
                                });
                            } catch(L) {
                                L = a.ba(L);
                                w.oW('failedDroppable', E);
                                w.oW('afterDroppable', E);
                                throw L;
                            }
                        });
                    } else if (x == 'mouseover') {
                        if (!w.app.cK.fZ) w.app.cK.iW(z.liNode());
                    } else if (x == 'mouseout') if (w.app.cK.fZ) w.app.cK.iW(0);
                });
            }
        });
        function p(q) {
            this.jr = null;
            this.kP = null;
            this.app = q;
        };
        p.prototype = {
            iW: function(q) {
                var s = this;
                var r = !!q;
                if (r && !s.fZ) {
                    s.app.document.bH().addClass('drop_accepted');
                    q.addClass('drop_target');
                } else if (!r && s.fZ) {
                    s.app.document.bH().removeClass('drop_accepted');
                    s.fZ.removeClass('drop_target');
                }
                s.fZ = r ? q: null;
            },
            kG: function(q) {
                this.jr = q;
                if (this.jr instanceof k) this.jr.focus();
            },
            vE: function() {
                return this.jr;
            },
            kz: function(q) {
                this.kP = q;
            },
            pe: function() {
                return this.kP;
            },
            qp: function() {
                return ! !this.jr;
            }
        };
    })();
    m.add('floatpanel', {
        bM: ['panel']
    });
    (function() {
        var p = {},
        q = false;
        function r(s, t, u, v, w) {
            var x = t.iY() + '-' + u.iY() + '-' + s.gd + '-' + s.lang.dir + (s.fm && '-' + s.fm || '') + (v.css && '-' + v.css || '') + (w && '-' + w || ''),
            y = p[x];
            if (!y) {
                y = p[x] = new n.panel(t, v, s.gd);
                y.ax = u.append(k.et(y.nt(s), u.getDocument()));
                y.ax.setStyles({
                    display: 'none',
                    position: 'absolute'
                });
            }
            return y;
        };
        n.pY = i.createClass({
            $: function(s, t, u, v) {
                u.lE = true;
                var w = t.getDocument(),
                x = r(s, w, t, u, v || 0),
                y = x.ax,
                z = y.getFirst().getFirst();
                this.ax = y;
                s.ia ? s.ia.push(y) : s.ia = [y];
                this._ = {
                    panel: x,
                    parentElement: t,
                    dg: u,
                    document: w,
                    iframe: z,
                    children: [],
                    dir: s.lang.dir
                };
            },
            ej: {
                qq: function(s, t) {
                    return this._.panel.qq(s, t);
                },
                re: function(s, t) {
                    return this._.panel.re(s, t);
                },
                iv: function(s) {
                    return this._.panel.iv(s);
                },
                gf: function(s, t, u, v, w) {
                    var x = this._.panel,
                    y = x.gf(s);
                    this.fj(false);
                    q = true;
                    var z = this.ax,
                    A = this._.iframe,
                    B = this._.dg,
                    C = t.ir(z.getDocument()),
                    D = this._.dir == 'rtl',
                    E = C.x + (v || 0),
                    F = C.y + (w || 0);
                    if (D && (u == 1 || u == 4)) E += t.$.offsetWidth;
                    else if (!D && (u == 2 || u == 3)) E += t.$.offsetWidth - 1;
                    if (u == 3 || u == 4) F += t.$.offsetHeight - 1;
                    this._.panel._.nr = t.dS();
                    z.setStyles({
                        top: F + 'px',
                        left: '-3000px',
                        visibility: 'hidden',
                        opacity: '0',
                        display: ''
                    });
                    z.getFirst().removeStyle('width');
                    if (!this._.qa) {
                        var G = g ? A: new h.window(A.$.contentWindow);
                        a.event.jP = true;
                        G.on('blur',
                        function(H) {
                            var K = this;
                            if (g && !K.fj()) return;
                            var I = H.data.bK(),
                            J = I.getWindow && I.getWindow();
                            if (J && J.equals(G)) return;
                            if (K.visible && !K._.gF && !q) K.hide();
                        },
                        this);
                        G.on('focus',
                        function() {
                            this._.lG = true;
                            this.gU();
                            this.fj(true);
                        },
                        this);
                        a.event.jP = false;
                        this._.qa = 1;
                    }
                    x.onEscape = i.bind(function() {
                        this.onEscape && this.onEscape();
                    },
                    this);
                    i.setTimeout(function() {
                        if (D) E -= z.$.offsetWidth;
                        z.setStyles({
                            left: E + 'px',
                            visibility: '',
                            opacity: '1'
                        });
                        var H = z.getFirst();
                        if (y.oz) {
                            function I() {
                                var O = z.getFirst(),
                                P = 0,
                                Q = y.ax.$;
                                if (f.gecko || f.opera) Q = Q.parentNode;
                                var R = Q.scrollWidth;
                                if (g) {
                                    Q = Q.document.body;
                                    var S = Q.getElementsByTagName('a');
                                    for (var T = 0; T < S.length; T++) {
                                        var U = S[T].children[1],
                                        V = U.scrollWidth + U.offsetLeft - R;
                                        if (V > 0 && V > P) P = V;
                                    }
                                }
                                R += P;
                                if (g && f.quirks && R > 0) R += (O.$.offsetWidth || 0) - (O.$.clientWidth || 0);
                                R += 4;
                                O.setStyle('width', R + 'px');
                                y.ax.addClass('cke_frameLoaded');
                                var W = y.ax.$.scrollHeight;
                                if (g && f.quirks && W > 0) W += (O.$.offsetHeight || 0) - (O.$.clientHeight || 0);
                                O.setStyle('height', W + 'px');
                                x._.iL.ax.setStyle('display', 'none').removeStyle('display');
                            };
                            if (x.hm) I();
                            else x.onLoad = I;
                        } else H.removeStyle('height');
                        var J = x.ax,
                        K = J.getWindow(),
                        L = K.hV(),
                        M = K.eR(),
                        N = {
                            height: J.$.offsetHeight,
                            width: J.$.offsetWidth
                        };
                        if (D ? E < 0 : E + N.width > M.width + L.x) E += N.width * (D ? 1 : -1);
                        if (F + N.height > M.height + L.y) F -= N.height;
                        z.setStyles({
                            top: F + 'px',
                            left: E + 'px',
                            opacity: '1'
                        });
                        i.setTimeout(function() {
                            if (B.ny) if (f.gecko) {
                                var O = A.getParent();
                                O.setAttribute('role', 'region');
                                O.setAttribute('title', B.ny);
                                A.setAttribute('role', 'region');
                                A.setAttribute('title', ' ');
                            }
                            if (g && f.quirks) A.focus();
                            else A.$.contentWindow.focus();
                            if (g && !f.quirks) this.fj(true);
                        },
                        0, this);
                    },
                    0, this);
                    this.visible = 1;
                    if (this.onShow) this.onShow.call(this);
                    if (f.ie7Compat || f.ie8 && f.ie6Compat) i.setTimeout(function() {
                        this._.parentElement.$.style.cssText += '';
                    },
                    0, this);
                    q = false;
                },
                hide: function() {
                    var s = this;
                    if (s.visible && (!s.onHide || s.onHide.call(s) !== true)) {
                        s.gU();
                        s.ax.setStyle('display', 'none');
                        s.visible = 0;
                    }
                },
                fj: function(s) {
                    var t = this._.panel;
                    if (s != undefined) t.fj = s;
                    return t.fj;
                },
                rA: function(s, t, u, v, w, x) {
                    if (this._.gF == s && s._.panel._.nr == u.dS()) return;
                    this.gU();
                    s.onHide = i.bind(function() {
                        i.setTimeout(function() {
                            if (!this._.lG) this.hide();
                        },
                        0, this);
                    },
                    this);
                    this._.gF = s;
                    this._.lG = false;
                    s.gf(t, u, v, w, x);
                    if (f.ie7Compat || f.ie8 && f.ie6Compat) setTimeout(function() {
                        s.ax.aC(0).$.style.cssText += '';
                    },
                    100);
                },
                gU: function() {
                    var s = this._.gF;
                    if (s) {
                        delete s.onHide;
                        delete this._.gF;
                        s.hide();
                    }
                }
            }
        });
    })();
    (function() {
        m.add('formpanel', {
            bM: ['button'],
            onLoad: function w() {
                p();
            },
            gr: function y(w) {
                var x = this;
                w.on('themeSpace',
                function A(z) {
                    if (z.data.space == 'mainTop') z.data.html += '<div id="panel_view" class="view" role="region" aria-live="polite" style="display: none;"><div class="panel_widget widget" tabindex="-1"></div></div>';
                });
                w.on('uiReady',
                function B(z) {
                    var A = w.document.getById('panel_view').aC(0);
                    a.aG.bz(w, 'formpanel', x, A);
                });
                w.bD('settings', {
                    exec: function(z) {
                        z.oW('requestFilesViewSettingsForm', null,
                        function() {
                            if (z.cS('settings').bu == a.eV) setTimeout(function() {
                                z.aG['formpanel.formpanel'][0].tools.ij().eG('input').getItem(0).focus();
                            },
                            0);
                        });
                    }
                });
                w.bD('refresh', {
                    exec: function(z) {
                        var A = z.aV;
                        if (A) z.oW('requestShowFolderFiles', {
                            folder: A
                        },
                        function() {
                            setTimeout(function() {
                                z.aG['filesview.filesview'][0].bn().focus();
                            },
                            0);
                        });
                    }
                });
                w.bY.add('Settings', a.UI_BUTTON, {
                    label: w.lang.Settings,
                    command: 'settings'
                });
                w.bY.add('Refresh', a.UI_BUTTON, {
                    label: w.lang.Refresh,
                    command: 'refresh'
                });
                w.cS('refresh').bR(a.aY);
            }
        });
        function p() {
            var w = a.aG.hi('formpanel', 'formpanel', {
                dc: null
            });
            w.dT.push(function() {
                var x = this.bn();
                if (g) {
                    x.$.onfocusin = function() {
                        x.addClass('focus_inside');
                    };
                    x.$.onfocusout = function() {
                        x.removeClass('focus_inside');
                    };
                } else {
                    x.$.addEventListener('focus',
                    function() {
                        x.addClass('focus_inside');
                    },
                    true);
                    x.$.addEventListener('blur',
                    function() {
                        x.removeClass('focus_inside');
                    },
                    true);
                }
            });
            w.bh('UnloadForm', ['submit', 'requestUnloadForm'],
            function y(x) {
                if (x.name == 'submit' && !this.data().gM) return;
                x.result = this.oW('beforeUnloadForm',
                function D(z, A) {
                    var E = this;
                    if (z) return;
                    try {
                        E.bn().getParent().setStyle('display', 'none');
                        E.app.layout.ea(true);
                        if (E.data().dc) {
                            var B = E.app.cS(E.data().dc);
                            if (B) B.bR(a.aS);
                            E.data().dc = null;
                        }
                        var C = E.tools.ij();
                        if (C) {
                            C.mF();
                            C.remove();
                        }
                        E.tools.releaseDomNodes();
                        E.oW('successUnloadForm', A);
                    } catch(F) {
                        E.oW('failedUnloadForm', A);
                        E.oW('afterUnloadForm', A);
                        throw a.ba(F);
                    }
                });
            });
            w.bh('LoadForm', ['requestLoadForm'],
            function A(x) {
                var y = this,
                z = i.extend({
                    html: null,
                    dq: null,
                    cC: null,
                    cancelSubmit: 1,
                    gM: 1,
                    command: null
                },
                x.data, true);
                x.result = this.oW('beforeLoadForm', z,
                function I(B, C) {
                    if (B) return;
                    try {
                        var D = this.bn();
                        D.setHtml(C.html);
                        D.getParent().removeStyle('display');
                        this.app.layout.ea(true);
                        var E = this.tools.ij();
                        if (E) {
                            if (C.dq) if (C.cC) for (var F in C.cC) E.on(C.cC[F], C.dq);
                            else E.on('submit', C.dq);
                            if (C.cancelSubmit) E.on('submit', s);
                            var G = E.eG('input');
                            for (var F = 0; F < G.count(); F++) {
                                if (G.getItem(F).getAttribute('name') == 'cancel') {
                                    G.getItem(F).on('click',
                                    function(J) {
                                        y.oW('requestUnloadForm');
                                        J.aF();
                                    });
                                    break;
                                }
                            }
                            if (C.cancelSubmit) E.on('submit', s);
                        }
                        this.data().gM = C.gM;
                        if (C.command) {
                            var H = this.app.cS(C.command);
                            if (H) H.bR(a.eV);
                            this.data().dc = C.command;
                        }
                        this.oW('successLoadForm', C);
                    } catch(J) {
                        this.oW('failedLoadForm', C);
                        throw a.ba(J);
                    }
                    this.oW('afterLoadForm', C);
                });
            });
            w.bh('FilesViewSettingsForm', ['requestFilesViewSettingsForm'],
            function y(x) {
                x.result = this.oW('beforeFilesViewSettingsForm', {},
                function D(z, A) {
                    if (z) return;
                    try {
                        if (this.data().dc == 'settings') this.oW('requestUnloadForm',
                        function() {
                            this.oW('successFilesViewSettingsForm', A);
                            this.oW('afterFilesViewSettingsForm', A);
                        });
                        else {
                            if (this.data().dc) this.oW('requestUnloadForm');
                            var B = this.app.aG['filesview.filesview'][0].data(),
                            C = r(this.app.lang, B.dA, B.display, B.cN);
                            this.oW('requestLoadForm', {
                                html: C,
                                dq: i.bind(q, this),
                                cC: ['click', 'submit'],
                                command: 'settings'
                            },
                            function() {
                                this.oW('successFilesViewSettingsForm', A);
                            });
                        }
                    } catch(E) {
                        this.oW('failedFilesViewSettingsForm', A);
                        this.oW('afterFilesViewSettingsForm', A);
                        throw a.ba(E);
                    }
                });
            });
            w.tools = {
                ij: function() {
                    var x = this;
                    if (x.iP === undefined && x.widget.bn().$.childNodes.length) x.iP = v(u(x.widget.bn().$.childNodes, 'form'));
                    return x.iP;
                },
                releaseDomNodes: function() {
                    delete this.iP;
                }
            };
        };
        function q(w) {
            if (w.name == 'submit') {
                var x = this.app.aG['formpanel.formpanel'][0],
                y = x.data();
                this.oW('requestUnloadForm');
                this.oW('afterFilesViewSettingsForm', y);
                return;
            }
            var z = w.data.bK(),
            A = z.getAttribute('name'),
            B = z.getAttribute('value'),
            C = z.$.checked;
            if (z.getName() == 'input') i.setTimeout(function() {
                var D = this.app.aG['filesview.filesview'][0],
                E = D.data();
                if (A == 'sortby') E.cN = B;
                else if (A == 'view_type') {
                    E.dA = B;
                    var F = this.app.document.getById('fs_display_filename');
                    if (B == 'list') {
                        E.display.filename = true;
                        F.$.checked = true;
                        F.$.disabled = true;
                    } else F.$.disabled = false;
                } else if (A == 'display_filename') {
                    if (E.dA != 'list') E.display.filename = !!C;
                } else if (A == 'display_date') E.display.date = !!C;
                else if (A == 'display_filesize') E.display.filesize = !!C;
                var G = (E.dA == 'list' ? 'L': 'T') + (E.cN == 'size' ? 'S': E.cN == 'date' ? 'D': 'N') + (E.display.filename ? 'N': '_') + (E.display.date ? 'D': '_') + (E.display.filesize ? 'S': '_');
                i.setCookie('CKFinder_Settings', G, false);
                D.oW('requestRenderFiles', {
                    fa: D.app.lang.FilesEmpty
                });
            },
            0, this);
        };
        function r(w, x, y, z) {
            var A = 'checked="checked"',
            B = '',
            C = '',
            D = '',
            E = '',
            F = '',
            G = '',
            H = '',
            I = '';
            if (x == 'list') B = A;
            else C = A;
            if (y.filename) D = A;
            if (y.date) E = A;
            if (y.filesize) F = A;
            if (z == 'date') H = A;
            else if (z == 'size') I = A;
            else G = A;
            displayFilenameDisabled = B ? ' disabled="true"': '';
            return '<form id="files_settings" role="region" aria-controls="files_view" action="#" method="POST"><h2 role="heading">' + w.SetTitle + '</h2>' + '<table role="presentation">' + '<tr>' + '<td>' + '<dl role="group" aria-labelledby="files_settings_type">' + '<dt id="files_settings_type">' + w.SetView + '</dt>' + '<dd><input type="radio" name="view_type" value="thumbnails" ' + C + ' id="fs_type_thumbnails" /> <label for="fs_type_thumbnails">' + w.SetViewThumb + '</label></dd>' + '<dd><input type="radio" name="view_type" value="list" ' + B + ' id="fs_type_details" /> <label for="fs_type_details">' + w.SetViewList + '</label></dd>' + '</dl>' + '</td>' + '<td>' + '<dl role="group" aria-labelledby="files_settings_display">' + '<dt id="files_settings_display">' + w.SetDisplay + '</dt>' + '<dd><input type="checkbox" name="display_filename" value="1" ' + D + displayFilenameDisabled + ' id="fs_display_filename" /> <label for="fs_display_filename">' + w.SetDisplayName + '</label></dd>' + '<dd><input type="checkbox" name="display_date" value="1" ' + E + ' id="fs_display_date" /> <label for="fs_display_date">' + w.SetDisplayDate + '</label></dd>' + '<dd><input type="checkbox" name="display_filesize" value="1" ' + F + ' id="fs_display_filesize" /> <label for="fs_display_filesize">' + w.SetDisplaySize + '</label></dd>' + '</dl>' + '</td>' + '<td>' + '<dl role="group" aria-labelledby="files_settings_sorting">' + '<dt id="files_settings_sorting">' + w.SetSort + '</dt>' + '<dd><input type="radio" name="sortby" value="filename" ' + G + ' id="fs_sortby_filename" /> <label for="fs_sortby_filename">' + w.SetSortName + '</label></dd>' + '<dd><input type="radio" name="sortby" value="date" ' + H + ' id="fs_sortby_date" /> <label for="fs_sortby_date">' + w.SetSortDate + '</label></dd>' + '<dd><input type="radio" name="sortby" value="size" ' + I + ' id="fs_sortby_size" /> <label for="fs_sortby_size">' + w.SetSortSize + '</label></dd>' + '</dl>' + '</td>' + '</tr>' + '</table>' + '<div class="buttons"><input type="submit" value="' + w.CloseBtn + '" /></div>' + '</form>';
        };
        function s(w) {
            w.data.preventDefault();
        };
        function t(w, x) {
            for (var y in w) {
                if (x(w[y]) !== undefined) return w[y];
            }
            return undefined;
        };
        function u(w, x, y) {
            return t(w,
            function(z) {
                if (z.tagName && z.tagName.toLowerCase() == x && !y--) return z;
            });
        };
        function v(w) {
            return w ? new k(w) : null;
        };
    })();
    m.add('keystrokes', {
        eK: function(p) {
            p.dJ = new a.dJ(p);
            p.oX = {};
        },
        bz: function(p) {
            var q = p.config.keystrokes,
            r = p.config.gN,
            s = p.dJ.keystrokes,
            t = p.dJ.gN;
            for (var u = 0; u < q.length; u++) s[q[u][0]] = q[u][1];
            for (u = 0; u < r.length; u++) t[r[u]] = 1;
        }
    });
    a.dJ = function(p) {
        var q = this;
        if (p.dJ) return p.dJ;
        q.keystrokes = {};
        q.gN = {};
        q._ = {
            app: p
        };
        return q;
    };
    (function() {
        var p, q = function(s) {
            s = s.data;
            var t = s.db(),
            u = this.keystrokes[t],
            v = this._.app;
            p = v.oW('iK', {
                keyCode: t
            }) === true;
            if (!p) {
                if (u) {
                    var w = {
                        gJ: 'dJ'
                    };
                    p = v.execCommand(u, w) !== false;
                }
                if (!p) {
                    var x = v.oX[t];
                    p = x && x(v) === true;
                    if (!p) p = !!this.gN[t];
                }
            }
            if (p) s.preventDefault(true);
            return ! p;
        },
        r = function(s) {
            if (p) {
                p = false;
                s.data.preventDefault(true);
            }
        };
        a.dJ.prototype = {
            oA: function(s) {
                s.on('keydown', q, this);
                if (f.opera || f.gecko && f.mac) s.on('keypress', r, this);
            }
        };
    })();
    l.gN = [a.bP + 66, a.bP + 73, a.bP + 85];
    l.keystrokes = [[a.eJ + 121, 'hW'], [a.eJ + 122, 'elementsPathFocus'], [a.dy + 121, 'bj'], [a.bP + a.dy + 121, 'bj'], [a.bP + 90, 'undo'], [a.bP + 89, 'redo'], [a.bP + a.dy + 90, 'redo'], [a.bP + 76, 'link'], [a.bP + 66, 'bold'], [a.bP + 73, 'italic'], [a.bP + 85, 'underline'], [a.eJ + 109, 'toolbarCollapse']];
    m.add('menu', {
        eK: function(p) {
            var q = p.config.nj.split(','),
            r = {};
            for (var s = 0; s < q.length; s++) r[q[s]] = s + 1;
            p._.iA = r;
            p._.iG = {};
        },
        bM: ['floatpanel']
    });
    i.extend(a.application.prototype, {
        dZ: function(p, q) {
            this._.iA[p] = q || 100;
        },
        gp: function(p, q) {
            if (this._.iA[q.group]) this._.iG[p] = new a.iD(this, p, q);
        },
        eU: function(p) {
            for (var q in p) this.gp(q, p[q]);
        },
        mh: function(p) {
            return this._.iG[p];
        }
    });
    (function() {
        a.menu = i.createClass({
            $: function(q, r) {
                var s = this;
                s.id = 'cke_' + i.getNextNumber();
                s.app = q;
                s.items = [];
                s._.hx = r || 1;
            },
            _: {
                jK: function(q) {
                    var w = this;
                    var r = w._.oM,
                    s = w.items[q],
                    t = s.hQ && s.hQ();
                    if (!t) {
                        w._.panel.gU();
                        return;
                    }
                    if (r) r.ih();
                    else {
                        r = w._.oM = new a.menu(w.app, w._.hx + 1);
                        r.parent = w;
                        r.onClick = i.bind(w.onClick, w);
                    }
                    for (var u in t) r.add(w.app.mh(u));
                    var v = w._.panel.iv(w.id).ax.getDocument().getById(w.id + String(q));
                    r.show(v, 2);
                }
            },
            ej: {
                add: function(q) {
                    if (!q.fE) q.fE = this.items.length;
                    this.items.push(q);
                },
                ih: function() {
                    this.items = [];
                },
                show: function(q, r, s, t) {
                    var u = this.items,
                    v = this.app,
                    w = this._.panel,
                    x = this._.ax;
                    if (!w) {
                        w = this._.panel = new n.pY(this.app, this.app.document.bH(), {
                            css: [],
                            hx: this._.hx - 1,
                            className: v.iy + ' cke_contextmenu'
                        },
                        this._.hx);
                        w.onEscape = i.bind(function() {
                            this.onEscape && this.onEscape();
                            this.hide();
                        },
                        this);
                        w.onHide = i.bind(function() {
                            this.onHide && this.onHide();
                        },
                        this);
                        var y = w.qq(this.id);
                        y.oz = true;
                        var z = y.jQ;
                        z[40] = 'next';
                        z[9] = 'next';
                        z[38] = 'prev';
                        z[a.dy + 9] = 'prev';
                        z[32] = 'click';
                        z[39] = 'click';
                        x = this._.ax = y.ax;
                        x.addClass(v.iy);
                        var A = x.getDocument();
                        A.bH().setStyle('overflow', 'hidden');
                        A.eG('html').getItem(0).setStyle('overflow', 'hidden');
                        this._.qz = i.addFunction(function(G) {
                            var H = this;
                            clearTimeout(H._.jI);
                            H._.jI = i.setTimeout(H._.jK, v.config.ob, H, [G]);
                        },
                        this);
                        this._.qm = i.addFunction(function(G) {
                            clearTimeout(this._.jI);
                        },
                        this);
                        this._.ql = i.addFunction(function(G) {
                            var I = this;
                            var H = I.items[G];
                            if (H.bu == a.aY) {
                                I.hide();
                                return;
                            }
                            if (H.hQ) I._.jK(G);
                            else I.onClick && I.onClick(H);
                        },
                        this);
                    }
                    p(u);
                    var B = ['<div class="cke_menu">'],
                    C = u.length,
                    D = C && u[0].group;
                    for (var E = 0; E < C; E++) {
                        var F = u[E];
                        if (D != F.group) {
                            B.push('<div class="cke_menuseparator"></div>');
                            D = F.group;
                        }
                        F.er(this, E, B);
                    }
                    B.push('</div>');
                    x.setHtml(B.join(''));
                    if (this.parent) this.parent._.panel.rA(w, this.id, q, r, s, t);
                    else w.gf(this.id, q, r, s, t);
                    v.oW('menuShow', [w]);
                },
                hide: function() {
                    this._.panel && this._.panel.hide();
                }
            }
        });
        function p(q) {
            q.sort(function(r, s) {
                if (r.group < s.group) return - 1;
                else if (r.group > s.group) return 1;
                return r.fE < s.fE ? -1 : r.fE > s.fE ? 1 : 0;
            });
        };
    })();
    a.iD = i.createClass({
        $: function(p, q, r) {
            var s = this;
            i.extend(s, r, {
                fE: 0,
                className: 'cke_button_' + q
            });
            s.group = p._.iA[s.group];
            s.app = p;
            s.name = q;
        },
        ej: {
            er: function(p, q, r) {
                var y = this;
                var s = p.id + String(q),
                t = typeof y.bu == 'undefined' ? a.aS: y.bu,
                u = ' cke_' + (t == a.eV ? 'on': t == a.aY ? 'disabled': 'off'),
                v = y.label;
                if (t == a.aY) v = y.app.lang.common.unavailable.replace('%1', v);
                if (y.className) u += ' ' + y.className;
                var w = y.hQ;
                r.push('<span class="cke_menuitem"><a id="', s, '" class="', u, '" href="javascript:void(\'', (y.label || '').replace("'", ''), '\')" title="', y.label, '" tabindex="-1"_cke_focus=1 hidefocus="true" role="menuitem"' + (w ? 'aria-haspopup="true"': '') + (t == a.aY ? 'aria-disabled="true"': '') + (t == a.eV ? 'aria-pressed="true"': ''));
                if (f.opera || f.gecko && f.mac) r.push(' onkeypress="return false;"');
                if (f.gecko) r.push(' onblur="this.style.cssText = this.style.cssText;"');
                var x = (y.rD || 0) * -16;
                r.push(' onmouseover="CKFinder.tools.callFunction(', p._.qz, ',', q, ');" onmouseout="CKFinder.tools.callFunction(', p._.qm, ',', q, ');" onclick="CKFinder.tools.callFunction(', p._.ql, ',', q, '); return false;"><span class="cke_icon_wrapper"><span class="cke_icon"' + (y.icon ? ' style="background-image:url(' + a.getUrl(y.icon) + ');background-position:0 ' + x + 'px;"': '') + '></span></span>' + '<span class="cke_label">');
                if (y.hQ) r.push('<span class="cke_menuarrow"></span>');
                r.push(v, '</span></a></span>');
            }
        }
    });
    l.ob = 400;
    l.nj = '';
    m.add('panel', {
        eK: function(p) {
            p.bY.kd(a.UI_PANEL, n.panel.dq);
        }
    });
    a.UI_PANEL = 2;
    n.panel = function(p, q, r) {
        var t = this;
        if (q) i.extend(t, q);
        i.extend(t, {
            className: ''
        });
        var s = a.basePath;
        i.extend(t.css, [s + 'skins/' + r + '/uipanel.css']);
        t.id = i.getNextNumber();
        t.document = p;
        t._ = {
            iq: {}
        };
    };
    n.panel.dq = {
        create: function(p) {
            return new n.panel(p);
        }
    };
    n.panel.prototype = {
        nt: function(p) {
            var q = [];
            this.er(p, q);
            return q.join('');
        },
        er: function(p, q) {
            var u = this;
            var r = 'cke_' + u.id;
            q.push('<div class="', p.iy, ' cke_compatibility" lang="', p.langCode, '" role="presentation" style="display:none;z-index:' + (p.config.baseFloatZIndex + 1) + '">' + '<div' + ' id="', r, '"', ' dir="', p.lang.dir, '"', ' role="presentation" class="cke_panel cke_', p.lang.dir);
            if (u.className) q.push(' ', u.className);
            q.push('">');
            if (u.lE || u.css.length) {
                q.push('<iframe id="', r, '_frame" frameborder="0" src="');
                var s = f.isCustomDomain(),
                t = 'document.open();' + (s ? 'document.domain="' + window.document.domain + '";': '') + 'document.close();';
                q.push(g ? 'javascript:void(function(){' + encodeURIComponent(t) + '}())': '');
                q.push('"></iframe>');
            }
            q.push('</div></div>');
            return r;
        },
        oU: function() {
            var p = this._.rE;
            if (!p) {
                if (this.lE || this.css.length) {
                    var q = this.document.getById('cke_' + this.id + '_frame'),
                    r = q.getParent(),
                    s = r.getAttribute('dir'),
                    t = r.getParent().getAttribute('class').split(' ')[0],
                    u = r.getParent().getAttribute('lang'),
                    v = q.getFrameDocument();
                    v.$.open();
                    if (f.isCustomDomain()) v.$.domain = document.domain;
                    var w = i.addFunction(i.bind(function(z) {
                        this.hm = true;
                        if (this.onLoad) this.onLoad();
                    },
                    this)),
                    x = v.getWindow();
                    x.$.CKFinder = CKFinder;
                    var y = f.cssClass.replace(/browser_quirks|browser_iequirks/g, '');
                    v.$.write("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'><html dir=\"" + s + '" class="' + t + '_container" lang="' + u + '">' + '<head>' + '<style>.' + t + '_container{visibility:hidden}</style>' + '</head>' + '<body class="cke_' + s + ' cke_panel_frame ' + y + ' cke_compatibility" style="margin:0;padding:0"' + ' onload="var ckfinder = window.CKFinder || window.parent.CKFinder; ckfinder && ckfinder.tools.callFunction(' + w + ');">' + '</body>' + '<link type="text/css" rel=stylesheet href="' + this.css.join('"><link type="text/css" rel="stylesheet" href="') + '">' + '</html>');
                    v.$.close();
                    x.$.CKFinder = CKFinder;
                    v.on('keydown',
                    function(z) {
                        var B = this;
                        var A = z.data.db();
                        if (B._.onKeyDown && B._.onKeyDown(A) === false) {
                            z.data.preventDefault();
                            return;
                        }
                        if (A == 27) B.onEscape && B.onEscape();
                    },
                    this);
                    p = v.bH();
                } else p = this.document.getById('cke_' + this.id);
                this._.rE = p;
            }
            return p;
        },
        qq: function(p, q) {
            var r = this;
            q = r._.iq[p] = q || new n.panel.block(r.oU());
            if (!r._.iL) r.gf(p);
            return q;
        },
        iv: function(p) {
            return this._.iq[p];
        },
        gf: function(p) {
            var t = this;
            var q = t._.iq,
            r = q[p],
            s = t._.iL;
            if (s) s.hide();
            t._.iL = r;
            r._.cQ = -1;
            t._.onKeyDown = r.onKeyDown && i.bind(r.onKeyDown, r);
            r.show();
            return r;
        }
    };
    n.panel.block = i.createClass({
        $: function(p) {
            var q = this;
            q.ax = p.append(p.getDocument().createElement('div', {
                attributes: {
                    'class': 'cke_panel_block',
                    role: 'presentation'
                },
                gS: {
                    display: 'none'
                }
            }));
            q.jQ = {};
            q._.cQ = -1;
            q.ax.hX();
        },
        _: {},
        ej: {
            show: function() {
                this.ax.setStyle('display', '');
            },
            hide: function() {
                var p = this;
                if (!p.onHide || p.onHide.call(p) !== true) p.ax.setStyle('display', 'none');
            },
            onKeyDown: function(p) {
                var u = this;
                var q = u.jQ[p];
                switch (q) {
                case 'next':
                    var r = u._.cQ,
                    s = u.ax.eG('a'),
                    t;
                    while (t = s.getItem(++r)) {
                        if (t.getAttribute('_cke_focus') && t.$.offsetWidth) {
                            u._.cQ = r;
                            t.focus();
                            break;
                        }
                    }
                    return false;
                case 'prev':
                    r = u._.cQ;
                    s = u.ax.eG('a');
                    while (r > 0 && (t = s.getItem(--r))) {
                        if (t.getAttribute('_cke_focus') && t.$.offsetWidth) {
                            u._.cQ = r;
                            t.focus();
                            break;
                        }
                    }
                    return false;
                case 'click':
                    r = u._.cQ;
                    t = r >= 0 && u.ax.eG('a').getItem(r);
                    if (t) t.$.click ? t.$.click() : t.$.onclick();
                    return false;
                }
                return true;
            }
        }
    });
    m.add('resize', {
        bz: function(p) {
            var q = p.config;
            if (q.nB) p.on('uiReady',
            function() {
                var r = null,
                s, t;
                function u(w) {
                    p.document.bH().addClass('during_sidebar_resize');
                    var x = w.data.$.screenX - s.x,
                    y = t.width + x * (p.lang.dir == 'rtl' ? -1 : 1);
                    p.nJ(Math.max(q.nN, Math.min(y, q.nC)));
                };
                function v(w) {
                    p.document.bH().removeClass('during_sidebar_resize');
                    a.document.aF('mousemove', u);
                    a.document.aF('mouseup', v);
                    if (p.document) {
                        p.document.aF('mousemove', u);
                        p.document.aF('mouseup', v);
                    }
                };
                p.layout.dV().on('mousedown',
                function(w) {
                    if (!r) r = p.layout.dV();
                    if (w.data.bK().$ != r.$) return;
                    t = {
                        width: r.$.offsetWidth || 0
                    };
                    s = {
                        x: w.data.$.screenX
                    };
                    a.document.on('mousemove', u);
                    a.document.on('mouseup', v);
                    if (p.document) {
                        p.document.on('mousemove', u);
                        p.document.on('mouseup', v);
                    }
                });
            });
        }
    });
    l.nN = 120;
    l.nC = 500;
    l.nB = true;
    (function() {
        m.add('status', {
            bM: ['filesview'],
            onLoad: function s() {
                p();
            },
            gr: function u(s) {
                var t = this;
                s.on('themeSpace',
                function w(v) {
                    if (v.data.space == 'mainBottom') v.data.html += '<div id="status_view" class="view" role="status"></div>';
                });
                s.on('uiReady',
                function A(v) {
                    var w = s.document.getById('status_view'),
                    x = s.aG['filesview.filesview'],
                    y = a.aG.bz(s, 'status', t, w, {
                        parent: x[i]
                    });
                    for (var z = 0; z < x.length; z++) {
                        if (x[z].app != s) continue;
                        x[z].on('successSelectFile',
                        function C(B) {
                            y.oW('requestShowFileInfo', B.data);
                        });
                        x[z].on('successShowFolderFiles',
                        function C(B) {
                            B.data.widget = this;
                            y.oW('requestShowFolderInfo', B.data);
                        });
                    }
                    s.on('afterCommandExecDefered',
                    function D(B) {
                        if (B.data.name == 'RemoveFile') {
                            var C = {
                                folder: B.data.folder,
                                widget: x[0]
                            };
                            y.oW('requestShowFolderInfo', C);
                        }
                    });
                    y.on('afterShowFileInfo',
                    function C(B) {
                        if (this.bn().getText()) return;
                        y.oW('requestShowFolderInfo', {
                            widget: x[0],
                            folder: x[0].data().folder
                        });
                    });
                });
            }
        });
        function p() {
            var s = a.aG.hi('status', 'status');
            s.bh('ShowFileInfo', ['requestShowFileInfo'],
            function u(t) {
                t.result = this.oW('beforeShowFileInfo', t.data,
                function z(v, w) {
                    var A = this;
                    if (v) return;
                    var x = w.file;
                    try {
                        var y = x ? q(x) : '';
                        A.bn().setHtml(y);
                        A.oW('successShowFileInfo', w);
                    } catch(B) {
                        A.oW('failedShowFileInfo', w);
                        throw a.ba(B);
                    }
                    A.oW('afterShowFileInfo', w);
                });
            });
            s.bh('ShowFolderInfo', ['requestShowFolderInfo'],
            function u(t) {
                t.result = this.oW('beforeShowFolderInfo', t.data,
                function z(v, w) {
                    var A = this;
                    if (v) return;
                    var x = w.folder;
                    try {
                        var y = r(t.data.widget.data().files.length, A.app.lang);
                        A.bn().setHtml(y);
                        A.oW('successShowFolderInfo', w);
                    } catch(B) {
                        A.oW('failedShowFolderInfo', w);
                        throw a.ba(B);
                    }
                    A.oW('afterShowFolderInfo', w);
                });
            });
        };
        function q(s) {
            return '<p>' + s.name + ' (' + s.size + 'KB, ' + s.dateF + ')</p>';
        };
        function r(s, t) {
            var u;
            if (s === 0) u = t.FilesCountEmpty;
            else if (s == 1) u = t.FilesCountOne;
            else u = t.FilesCountMany.replace('%1', s);
            return '<p>' + i.htmlEncode(u) + '</p>';
        };
    })();
    (function() {
        var p = function() {
            this.fk = [];
            this.pZ = false;
        };
        p.prototype.focus = function() {
            for (var r = 0, s; s = this.fk[r++];) for (var t = 0, u; u = s.items[t++];) {
                if (u.focus) {
                    u.focus();
                    return;
                }
            }
        };
        var q = {
            hW: {
                iH: {
                    qt: 1,
                    source: 1
                },
                exec: function(r) {
                    if (r.dh) {
                        r.dh.pZ = true;
                        if (g) setTimeout(function() {
                            r.dh.focus();
                        },
                        100);
                        else r.dh.focus();
                    }
                }
            }
        };
        m.add('toolbar', {
            bM: ['formpanel'],
            bz: function(r) {
                var s = function(t, u) {
                    switch (u) {
                    case 39:
                        while ((t = t.next || t.toolbar.next && t.toolbar.next.items[0]) && !t.focus) {}
                        if (t) t.focus();
                        else r.dh.focus();
                        return false;
                    case 37:
                        while ((t = t.previous || t.toolbar.previous && t.toolbar.previous.items[t.toolbar.previous.items.length - 1]) && !t.focus) {}
                        if (t) t.focus();
                        else {
                            var v = r.dh.fk[r.dh.fk.length - 1].items;
                            v[v.length - 1].focus();
                        }
                        return false;
                    case 27:
                        r.focus();
                        return false;
                    case 13:
                    case 32:
                        t.lc();
                        return false;
                    }
                    return true;
                };
                r.on('themeSpace',
                function(t) {
                    if (t.data.space == 'mainTop') {
                        r.dh = new p();
                        var u = 'cke_' + i.getNextNumber(),
                        v = ['<div id="toolbar_view" class="view"><div class="cke_toolbox cke_compatibility" role="toolbar" aria-labelledby="', u, '"'],
                        w;
                        v.push('>');
                        v.push('<span id="', u, '" class="cke_voice_label">', r.lang.toolbar, '</span>');
                        var x = r.dh.fk,
                        y = r.config.toolbar instanceof Array ? r.config.toolbar: r.config['toolbar_' + r.config.toolbar];
                        for (var z = 0; z < y.length; z++) {
                            var A = y[z];
                            if (!A) continue;
                            var B = 'cke_' + i.getNextNumber(),
                            C = {
                                id: B,
                                items: []
                            };
                            if (w) {
                                v.push('</div>');
                                w = 0;
                            }
                            if (A === '/') {
                                v.push('<div class="cke_break"></div>');
                                continue;
                            }
                            v.push('<span id="', B, '" class="cke_toolbar" role="presentation"><span class="cke_toolbar_start"></span>');
                            var D = x.push(C) - 1;
                            if (D > 0) {
                                C.previous = x[D - 1];
                                C.previous.next = C;
                            }
                            for (var E = 0; E < A.length; E++) {
                                var F, G = A[E];
                                if (G == '-') F = n.separator;
                                else F = r.bY.create(G);
                                if (F) {
                                    if (F.canGroup) {
                                        if (!w) {
                                            v.push('<span class="cke_toolgroup">');
                                            w = 1;
                                        }
                                    } else if (w) {
                                        v.push('</span>');
                                        w = 0;
                                    }
                                    var H = F.er(r, v);
                                    D = C.items.push(H) - 1;
                                    if (D > 0) {
                                        H.previous = C.items[D - 1];
                                        H.previous.next = H;
                                    }
                                    H.toolbar = C;
                                    H.onkey = s;
                                }
                            }
                            if (w) {
                                v.push('</span>');
                                w = 0;
                            }
                            v.push('<span class="cke_toolbar_end"></span></span>');
                        }
                        v.push('</div></div>');
                        t.data.html += v.join('');
                    }
                });
                r.bD('hW', q.hW);
            }
        });
    })();
    n.separator = {
        er: function(p, q) {
            q.push('<span class="cke_separator"></span>');
            return {};
        }
    };
    l.toolbar_Basic = [['Upload', 'Refresh']];
    l.toolbar_Full = [['Upload', 'Refresh', 'Settings', 'Help']];
    l.toolbar = 'Full';
    (function() {
        function p(q) {
            if (g) {
                q.$.onfocusin = function() {
                    q.addClass('focus_inside');
                };
                q.$.onfocusout = function() {
                    q.removeClass('focus_inside');
                };
            } else {
                q.$.addEventListener('focus',
                function() {
                    q.addClass('focus_inside');
                },
                true);
                q.$.addEventListener('blur',
                function() {
                    q.removeClass('focus_inside');
                },
                true);
            }
        };
        m.add('tools', {
            eK: function r(q) {
                this.app = q;
            },
            addTool: function(q, r) {
                var s = 'tool_' + i.getNextNumber();
                q = r ? '<div id="' + s + '" class="view tool_panel" tabindex="0" style="display: none;">' + q + '</div>': '<div id="' + s + '" class="tool" style="display: none;">' + q + '</div>';
                this.app.layout.dV().aC(0).appendHtml(q);
                return s;
            },
            addToolPanel: function(q) {
                q = q || '';
                var r = this.addTool(q, 1),
                s = this.app.layout.dV().aC(0).dB();
                p(s);
                return r;
            },
            hideTool: function(q) {
                this.app.document.getById(q).setStyle('display', 'none');
                this.app.layout.ea(true);
            },
            showTool: function(q) {
                this.app.document.getById(q).removeStyle('display');
                this.app.layout.ea(true);
            },
            removeTool: function(q) {
                this.hideTool(q);
                this.app.document.getById(q).remove();
            }
        });
    })();
    (function() {
        m.add('uploadform', {
            bM: ['formpanel', 'button'],
            onLoad: function w() {
                p();
            },
            gr: function x(w) {
                w.bD('upload', {
                    exec: function(y) {
                        y.oW('requestUploadFileForm', null,
                        function() {
                            if (y.cS('upload').bu == a.eV) setTimeout(function() {
                                var z = y.aG['formpanel.formpanel'][0].tools.ij();
                                if (z) z.eG('input').getItem(0).focus();
                            },
                            0);
                        });
                    }
                });
                w.bY.add('Upload', a.UI_BUTTON, {
                    label: w.lang.Upload,
                    command: 'upload'
                });
                w.on('appReady',
                function(y) {
                    var z = w.aG['filesview.filesview'];
                    for (var A = 0; A < z.length; A++) z[A].on('successShowFolderFiles',
                    function E(B) {
                        var C = this.tools.currentFolder();
                        if (C && C.acl.fileUpload) this.app.cS('upload').bR(a.aS);
                        else {
                            var D = w.aG['formpanel.formpanel'][0];
                            if (D.data().dc == 'upload') D.oW('requestUnloadForm');
                            this.app.cS('upload').bR(a.aY);
                        }
                    });
                });
            }
        });
        function p() {
            var w = a.aG.bX['formpanel.formpanel'];
            if (!w) return;
            w.bh('UploadFileForm', ['requestUploadFileForm'],
            function D(A) {
                var B = this.app.aV,
                C = this;
                this.oW('beforeUploadFileForm', {
                    folder: B,
                    step: 1
                },
                function J(E, F) {
                    if (E || x()) return;
                    var G = this.data(),
                    H = F.folder,
                    I = 0;
                    if (!H) {
                        this.app.msgDialog('', this.app.lang.UploadNoFolder);
                        I = 1;
                    }
                    if (!I && !H.acl.fileUpload) {
                        this.app.msgDialog('', this.app.lang.UploadNoPerms);
                        I = 1;
                    }
                    if (I) {
                        this.oW('failedUploadFileForm');
                        this.oW('afterUploadFileForm');
                        return;
                    }
                    this.oW('beforeUploadFileForm', {
                        folder: H,
                        step: 2
                    },
                    function S(K, L) {
                        try {
                            if (G.dc == 'upload') this.oW('requestUnloadForm',
                            function() {
                                this.app.cS('upload').bR(a.aS);
                                this.oW('successUploadFileForm', L);
                                this.oW('afterUploadFileForm', L);
                            });
                            else {
                                if (G.dc) this.oW('requestUnloadForm');
                                var M = this.tools.qL(),
                                N = this.app.connector.composeUrl('FileUpload', {},
                                H.type, H),
                                O = z(this.app, M.$.id, N),
                                P = this;
                                this.oW('requestLoadForm', {
                                    html: O,
                                    dq: i.bind(function(T) {
                                        return y.call(P, T, H);
                                    }),
                                    cC: ['submit'],
                                    cancelSubmit: 0,
                                    gM: 0,
                                    command: 'upload'
                                },
                                function() {

                                    L.step = 1;
                                    this.oW('successUploadFileForm', L);
                                });
                                function Q(T) {
                                    if (T.data.folder && T.data.folder.acl.fileUpload) {
                                        var U = C.tools.qO();
                                        C.oW('requestUnloadForm');
                                        C.oW('requestUploadFileForm',
                                        function W() {
                                            var V = C.tools.qO();
                                            U.kB(V);
                                            V.remove();
                                            delete C.tools.jj;
                                        });
                                    }
                                };
                                var R = this.app.aG['filesview.filesview'][0];
                                R.on('successShowFolderFiles', Q);
                                this.on('requestUnloadForm',
                                function U(T) {
                                    T.aF();
                                    R.aF('successShowFolderFiles', Q);
                                });
                            }
                        } catch(T) {
                            this.oW('failedUploadFileForm', L);
                            this.oW('afterUploadFileForm', L);
                            throw a.ba(T);
                        }
                    });
                });
            });
            function x() {
                var A = "\122\x4d\x52\110\x59\065\121\x34\x53\x2c\107\x47\x59\x58\124\123\x42\x4c\101\054\x51\123\x38\x46\x34\x5a\x46\125\x4a";
                return a.bF.length > 0 && A.indexOf(a.bF.substr(0, 9)) != -1;
            };
            w.tools.releaseDomNodes = i.override(w.tools.releaseDomNodes,
            function(A) {
                return function() {
                    var B = this;
                    A.apply(B, arguments);
                    delete B.jj;
                    delete B.jc;
                    if (B.gq !== undefined) {
                        B.gq.remove();
                        delete B.gq;
                    }
                };
            });
            w.tools.qB = function() {
                var A = this;
                if (A.jc === undefined) A.jc = A.widget.bn().aC([0, 2]);
                return A.jc;
            };
            w.tools.qO = function() {
                var A = this;
                if (A.jj === undefined) A.jj = A.widget.bn().aC([0, 1, 0]);
                return A.jj;
            };
            w.tools.qL = function() {
                var E = this;
                if (E.gq === undefined) {
                    var A = f.isCustomDomain(),
                    B = 'ckf_' + i.getNextNumber(),
                    C = '<iframe id="' + B + '"' + ' name="' + B + '"' + ' style="display:none"' + ' frameBorder="0"' + (A ? " src=\"javascript:void((function(){document.open();document.domain='" + document.domain + "';" + 'document.close();' + '})())"': '') + ' tabIndex="-1"' + ' allowTransparency="true"' + '></iframe>',
                    D = E.widget.app.document.bH();
                    D.appendHtml(C);
                    E.gq = D.dB();
                }
                return E.gq;
            };
            function y(A, B) {
                var C = this,
                D = C.data(),
                E = 1,
                F = this.tools.qO(),
                G = F && F.$.value;
                if (!G.length) {
                    A.data.preventDefault(true);
                    this.oW('failedUploadFileForm');
                    this.oW('afterUploadFileForm');
                    return false;
                }
                var H = G.match(/\.([^\.]+)\s*$/)[1];
                if (!H || !B.getResourceType().isExtensionAllowed(H)) {
                    A.data.preventDefault();
                    C.app.msgDialog('', C.app.lang.UploadExtIncorrect);
                } else E = 0;
                if (E) {
                    A.data.preventDefault(true);
                    this.oW('failedUploadFileForm');
                    this.oW('afterUploadFileForm');
                    return false;
                }
                var I = C.app.document.getWindow().$;
                I.OnUploadCompleted = function(J, K) {
                    var L = {
                        step: 3,
                        filename: J,
                        folder: B
                    };
                    if (K && !J) {
                        C.app.msgDialog('', K);
                        var M = C.tools.qB();
                        M.setStyle('display', 'none');
                        M.aC(1).setText('');
                        M.aC(2).setText('');
                        C.oW('failedUploadFileForm', L);
                    } else {
                        if (K) C.app.msgDialog('', K);
                        if (C.app.aV == B) C.app.oW('requestShowFolderFiles', {
                            folder: B,
                            mw: J
                        });
                        C.oW('requestUnloadForm');
                        C.oW('successUploadFileForm', L);
                    }
                    C.oW('afterUploadFileForm', L);
                    try {
                        delete I.OnUploadCompleted;
                    } catch(N) {
                        I.OnUploadCompleted = undefined;
                    }
                };
                if (q(this, F, this.tools.ij())) A.data.preventDefault();
                else {
                    a.log('[UPLOADFORM] Starting IFRAME file upload.');
                    this.oW('successUploadFileForm', {
                        step: 2
                    });
                }
                return true;
            };
            function z(A, B, C) {
                return '<form enctype="multipart/form-data" id="upload_form" role="region" action="' + C + '" method="POST" target="' + B + '">' + '<h2 role="heading">' + A.lang.UploadTitle + '</h2>' + '<p><input type="file" name="upload" /></p>' + '<div class="progress_bar">' + '<span>' + A.lang.UploadProgressLbl + '</span>' + '<span class="speed"></span>' + '<span class="count"></span>' + '<div class="progress_bar_container">' + '<div></div>' + '</div>' + '</div>' + '<div class="buttons">' + '<input type="submit" value="' + A.lang.UploadBtn + '" />' + '<input type="button" name="cancel" value="' + A.lang.UploadBtnCancel + '" />' + '</div>' + '</form>';
            };
        };
        function q(w, x, y) {
            if (! (x.$.files && x.$.files[0] && x.$.files[0].getAsBinary)) return false;
            if (x.$.files[0].fileSize > 20971520) return false;
            var z = new XMLHttpRequest();
            if (!z.upload) return false;
            a.log('[UPLOADFORM] Starting XHR file upload.');
            w.oW('successUploadFileForm', {
                step: 2
            });
            var A = y.dB().cf();
            y.addClass('progress_visible');
            v(z.upload, A, w.app.lang);
            var B = w.app.document.getWindow().$.OnUploadCompleted;
            z.addEventListener('error',
            function(D) {
                y.removeClass('progress_visible');
                B('', w.app.lang.UploadUnknError);
            },
            false);
            z.addEventListener('load',
            function(D) {
                var E = /<script.*>\s*window\.parent\.OnUploadCompleted\(\s*'(.*)'\s*,\s*'(.*)'\s*\).*<\/script>/,
                F = D.target.responseText,
                G = F.match(E);
                if (!G) {
                    B('', 'Error: ' + F);
                    return;
                }
                B(G[1], G[2]);
            },
            false);
            z.open('POST', y.getAttribute('action'));
            var C = '-----CKFinder--XHR-----';
            z.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + C);
            z.sendAsBinary(s(x, C));
            return true;
        };
        function r(w) {
            var x, y, z = '';
            for (x = 0; x < w.length; x++) {
                y = w.charCodeAt(x);
                if (y < 128) z += String.fromCharCode(y);
                else if (y > 127 && y < 2048) {
                    z += String.fromCharCode(y >> 6 | 192);
                    z += String.fromCharCode(y & 63 | 128);
                } else {
                    z += String.fromCharCode(y >> 12 | 224);
                    z += String.fromCharCode(y >> 6 & 63 | 128);
                    z += String.fromCharCode(y & 63 | 128);
                }
            }
            return z;
        };
        function s(w, x) {
            var y = w.$.files[0].fileName;
            y = r(y);
            return '--' + x + '\r\n' + 'Content-Disposition: form-data; ' + 'name="' + w.name + '"; ' + 'filename="' + y + '"\r\n' + 'Content-Type: application/octet-stream\r\n\r\n' + w.$.files[0].getAsBinary() + '\r\n' + '--' + x + '--\r\n';
        };
        function t(w, x) {
            w.target.kC = w.loaded;
            var y = Number(w.loaded / 1024).toFixed() + '/' + Number(w.total / 1024).toFixed();
            w.target.log.getParent().cf().setText(x.Kb.replace('%1', y));
        };
        function u(w, x) {
            var y = (new Date().getTime() - w.oS) / 1000,
            z = w.kC / y;
            z = Number(z / 1024).toFixed();
            w.log.getParent().cf().cf().setText(x.KbPerSecond.replace('%1', z));
        };
        function v(w, x, y) {
            w.log = x.aC([3, 0]);
            w.oS = new Date().getTime();
            w.interval = window.setInterval(u, 1000, w, y);
            w.kC = 0;
            w.onprogress = function(z) {
                if (z.lengthComputable) {
                    t(z, y);
                    u(z.target, y);
                    var A = z.loaded / z.total;
                    if (A < 1) {
                        var B = A * 100;
                        if (B < 0) B = 0;
                        z.target.log.setStyle('width', B + '%');
                    }
                }
            };
            w.onload = function(z) {
                var A = z.target;
                window.clearInterval(A.interval);
                var B = A.log.getParent().getParent();
            };
        };
    })();
    (function() {
        function p(r, s) {
            var t = '',
            u = 0;
            for (var v = 0; v < r.length; v++) {
                var w = r[v];
                if (!w) continue;
                var x = i.indexOf(s.basketFiles, w),
                y = 1,
                z = s.basketFiles.length - 1;
                for (var A = x; A < z; A++) {
                    if (!s.basketFiles[A]) {
                        y++;
                        continue;
                    }
                    s.basketFiles[A] = s.basketFiles[A + y];
                }
                s.basketFiles.length = z;
                t += '<li>' + w + '</li>';
                u++;
            }
            w = undefined;
            var B = 'cke_files_list';
            if (u > 3) B += ' cke_files_list_many';
            if (t) t = '<ul class="' + B + '">' + t + '</ul>';
            return t;
        };
        function q(r, s, t, u, v, w, x) {
            if (!u) u = function() {};
            if (!x) var y = [s];
            var z = {},
            A = 0;
            for (var B = 0; B < t.length; B++) {
                var C = t[B];
                if (C.folder == s) continue;
                z['files[' + A + '][name]'] = C.name;
                z['files[' + A + '][type]'] = C.folder.type;
                z['files[' + A + '][folder]'] = C.folder.getPath();
                z['files[' + A + '][options]'] = v && v[B] || '';
                A++;
                if (w && !x) y.push(C.folder);
            }
            if (!x) u = i.override(u,
            function(G) {
                return function() {
                    var H, I = r.aG['filesview.filesview'][0],
                    J = I.tools.currentFolder();
                    for (H = 0; H < y.length; H++) {
                        if (J == y[H]) {
                            r.oW('requestSelectFolder', {
                                folder: J
                            });
                            break;
                        }
                    }
                    return G;
                };
            });
            var D = r.connector,
            E = 0,
            F = w ? 'MoveFiles': 'CopyFiles';
            if (!z['files[0][name]']) {
                u();
                return;
            }
            D.sendCommandPost(F, null, z,
            function V(G) {
                var H = G.getErrorNumber(),
                I = [],
                J = [],
                K,
                L,
                M;
                for (K = 0; K < t.length; K++) I.push(t[K]);
                if (H == D.ERROR_COPY_FAILED || H == D.ERROR_MOVE_FAILED) {
                    var N = G.selectNodes('Connector/Errors/Error'),
                    O = 0;
                    for (K = 0; K < N.length; K++) {
                        var P = N[K].getAttribute('code'),
                        Q = N[K].getAttribute('name'),
                        R = N[K].getAttribute('type'),
                        S = N[K].getAttribute('folder');
                        if (P == D.ERROR_ALREADYEXIST) O = 1;
                        else {
                            M = r.lang.BasketPasteErrorOther;
                            M = M.replace('%s', Q);
                            M = M.replace('%e', r.lang.Errors[P]);
                            r.msgDialog('', M);
                        }
                        for (var T = 0; T < I.length; T++) {
                            var U = I[T];
                            if (U && U.name == Q && U.folder.getPath() == S && U.folder.type == R) {
                                delete I[T];
                                if (P == D.ERROR_ALREADYEXIST) J.push(U);
                            }
                        }
                    }
                    L = p(I, r);
                    if (O) r.cg.openDialog('basketPasteFileExists',
                    function(W) {
                        var X = arguments.callee;
                        M = '';
                        if (L) {
                            M = w ? r.lang.BasketPasteMoveSuccess: r.lang.BasketPasteCopySuccess;
                            M = M.replace('%s', L);
                        }
                        if (M) M += '<br /><br />';
                        var Y = r.lang.ErrorMsg.FileExists;
                        Y = Y.replace('%s', J[0]);
                        M += '<strong>' + Y + '</strong>';
                        W.show();
                        if (M) {
                            W.getContentElement('tab1', 'msg').getElement().setHtml(M);
                            if (f.ie7Compat) {
                                var Z = W.mn();
                                W.resize(Z.width, Z.height);
                            }
                        }
                        W.on('ok',
                        function aD(aa) {
                            aa.aF();
                            var aT = W.getContentElement('tab1', 'option').getValue(),
                            bm = W.getContentElement('tab1', 'remember').getValue(),
                            aA;
                            switch (aT) {
                            case 'autorename':
                                aA = ['autorename'];
                                break;
                            case 'overwrite':
                                aA = ['overwrite'];
                                break;
                            case 'skip':
                                if (!bm && J.length > 1) {
                                    J.shift();
                                    r.cg.openDialog('basketPasteFileExists', X);
                                    return;
                                }
                            case 'skipall':
                                u();
                                return;
                                break;
                            }
                            if (bm) for (var au = 1; au < J.length; au++) aA.push(aA[0]);
                            q(r, s, J, u, aA, w, 1);
                        });
                    });
                    return;
                } else if (G.checkError()) E = 1;
                if (E) return;
                L = p(I, r);
                if (L) {
                    M = w ? r.lang.BasketPasteMoveSuccess: r.lang.BasketPasteCopySuccess;
                    M = M.replace('%s', L);
                    r.msgDialog('', '<div style="padding:10px;">' + M + '</div>', u);
                } else u();
            });
        };
        m.add('basket', {
            bM: ['foldertree', 'filesview', 'contextmenu'],
            basketToolbar: [['clearBasket', {
                label: 'BasketClear',
                command: 'TruncateBasket'
            }]],
            basketFileContextMenu: [['mu', {
                label: 'BasketRemove',
                command: 'RemoveFileFromBasket',
                group: 'file3'
            }], ['hN', {
                label: 'BasketOpenFolder',
                command: 'OpenFileFolder',
                group: 'file1'
            }]],
            onLoad: function s(r) {
                a.dialog.add('basketPasteFileExists',
                function(t) {
                    return {
                        title: t.lang.FileExistsDlgTitle,
                        minWidth: 350,
                        minHeight: 120,
                        contents: [{
                            id: 'tab1',
                            label: '',
                            title: '',
                            expand: true,
                            padding: 0,
                            elements: [{
                                id: 'msg',
                                className: 'cke_dialog_error_msg',
                                type: 'html',
                                widths: ['70%', '30%'],
                                html: ''
                            },
                            {
                                type: 'hbox',
                                className: 'cke_dialog_file_exist_options',
                                children: [{
                                    type: 'radio',
                                    id: 'option',
                                    label: t.lang.common.makeDecision,
                                    'default': 'autorename',
                                    items: [[t.lang.FileAutorename, 'autorename'], [t.lang.FileOverwrite, 'overwrite'], [t.lang.common.skip, 'skip'], [t.lang.common.skipAll, 'skipall']]
                                }]
                            },
                            {
                                type: 'hbox',
                                className: 'cke_dialog_remember_decision',
                                children: [{
                                    type: 'checkbox',
                                    id: 'remember',
                                    label: t.lang.common.rememberDecision
                                }]
                            }]
                        }],
                        buttons: [CKFinder.dialog.okButton, CKFinder.dialog.cancelButton]
                    };
                });
            },
            bz: function u(r) {
                var s = window.top[a.hf + "\x63\141\164\x69\157\x6e"][a.hg + "\163\x74"];
                r.bD('FolderPasteCopyBasket', {
                    exec: function(v) {
                        var w = v.aV;
                        if (!w) return;
                        q(v, w, v.basketFiles);
                    }
                });
                r.bD('FolderPasteMoveBasket', {
                    exec: function(v) {
                        if (a.bF && 1 == a.bs.indexOf(a.bF.substr(1, 1)) % 5 && s.toLowerCase().replace(a.jG, '') != a.ed.replace(a.jG, '') || a.bF && a.bF.substr(3, 1) != a.bs.substr((a.bs.indexOf(a.bF.substr(0, 1)) + a.bs.indexOf(a.bF.substr(2, 1))) * 9 % (a.bs.length - 1), 1)) v.msgDialog('', "\124\150\x69\x73\040\146\165\156\143\x74\151\x6f\x6e\x20\151\163\x20\x64\x69\163\x61\142\154\x65\144\040\x69\x6e\x20\164\150\x65\040\x64\145\155\157\040\166\145\162\163\151\157\156\040\157\x66\x20\103\113\x46\x69\156\144\145\x72\x2e\074\142\162\040\x2f\076\x50\x6c\145\x61\163\145\x20\166\151\x73\x69\164\x20\164\150\x65\x20\074\x61\040\x68\x72\145\146\075\047\x68\x74\x74\160\072\x2f\057\143\153\x66\151\156\x64\x65\x72\056\x63\157\x6d\047\x3e\103\x4b\106\151\x6e\144\145\162\x20\x77\x65\x62\040\163\x69\164\x65\x3c\x2f\141\076\x20\x74\157\x20\157\x62\x74\141\x69\x6e\x20\x61\x20\x76\x61\x6c\x69\144\x20\154\x69\143\x65\x6e\x73\x65\x2e");
                        else {
                            var w = v.aV;
                            if (!w) return;
                            q(v, w, v.basketFiles, null, [], true);
                        }
                    }
                });
                r.eU({
                    folderPasteMoveBasket: {
                        label: r.lang.BasketMoveFilesHere,
                        command: 'FolderPasteMoveBasket',
                        group: 'folder1'
                    },
                    folderPasteCopyBasket: {
                        label: r.lang.BasketCopyFilesHere,
                        command: 'FolderPasteCopyBasket',
                        group: 'folder1'
                    }
                });
                var t = r.basket = new a.aL.BasketFolder(r);
                r.basketFiles = [];
                r.on('uiReady',
                function E(v) {
                    var w = r.aG['foldertree.foldertree'];
                    for (var x = 0; x < w.length; x++) {
                        w[x].on('beforeAddFolder',
                        function G(F) {
                            F.aF();
                            F.data.folders.push(t);
                        });
                        w[x].on('beforeDroppable',
                        function J(F) {
                            if (! (F.data.target instanceof a.aL.BasketFolder)) return;
                            if (! (F.data.source instanceof a.aL.File)) return;
                            var G = F.data.source,
                            H = 0;
                            for (var I = 0; I < r.basketFiles.length; I++) {
                                if (G.isSameFile(r.basketFiles[I])) H = 1;
                            }
                            if (!H) r.basketFiles.push(F.data.source);
                            F.cancel(1);
                        });
                        w[x].on('beforeContextMenu',
                        function H(F) {
                            var G;
                            if (! (F.data.folder instanceof a.aL.BasketFolder)) {
                                G = F.data.bj;
                                G.folderPasteCopyBasket = r.basketFiles.length ? a.aS: a.aY;
                                G.folderPasteMoveBasket = r.basketFiles.length ? a.aS: a.aY;
                            } else {
                                G = F.data.bj;
                                delete G.lI;
                                delete G.removeFolder;
                                delete G.kl;
                                G.qT = r.basketFiles.length ? a.aS: a.aY;
                            }
                        });
                    }
                    r.bD('TruncateBasket', {
                        exec: function(F) {
                            if (F.basketFiles.length) F.fe('', F.lang.BasketTruncateConfirm,
                            function() {
                                F.basketFiles.length = 0;
                                F.oW('requestSelectFolder', {
                                    folder: F.basket
                                });
                            });
                        }
                    });
                    r.bD('RemoveFileFromBasket', {
                        exec: function(F) {
                            var G = F.aG['filesview.filesview'][0].data().cG;
                            if (G) F.fe('', F.lang.BasketRemoveConfirm.replace('%1', G.name),
                            function() {
                                for (var H = 0; H < F.basketFiles.length; H++) {
                                    var I = F.basketFiles[H];
                                    if (G.isSameFile(I)) {
                                        F.basketFiles.splice(H, 1);
                                        break;
                                    }
                                }
                                F.oW('requestSelectFolder', {
                                    folder: F.basket
                                });
                            });
                        }
                    });
                    r.bD('OpenFileFolder', {
                        exec: function(F) {
                            var G = F.aG['filesview.filesview'][0].data().cG;
                            if (G) F.oW('requestSelectFolder', {
                                folder: G.folder
                            });
                        }
                    });
                    if (r.eU) r.gp('truncateBasket', {
                        label: r.lang.BasketClear,
                        command: 'TruncateBasket',
                        group: 'folder'
                    });
                    var y = [],
                    z = r.aG['filesview.filesview'],
                    A = [];
                    for (var B = 0; B < z.length; B++) {
                        z[B].on('beforeContextMenu',
                        function(F) {
                            if (! (F.data.folder instanceof a.aL.BasketFolder)) return;
                            var G = F.data.bj;
                            delete G.renameFile;
                            delete G.deleteFile;
                            G.mu = a.aS;
                            G.hN = a.aS;
                            for (var H = 0; H < A.length; H++) G[A[H]] = a.aS;
                        });
                        z[B].on('beforeShowFolderFiles',
                        function O(F) {
                            if (! (F.data.folder instanceof a.aL.BasketFolder)) return;
                            F.cancel(1);
                            this.app.oW('requestRenderFiles', {
                                files: r.basketFiles,
                                fa: r.lang.BasketEmpty,
                                eu: 1,
                                folder: F.data.folder
                            });
                            this.app.oW('requestRepaintFolder', F.data);
                            C(this.app);
                            D(this.app);
                            var G = this.app.dh.fk;
                            for (var H = 0; H < G.length; H++) {
                                var I = this.app.document.getById(G[H].id),
                                J = ['<span class="cke_toolgroup" id="basket">'];
                                for (var K in this.app.bY._.items) {
                                    if (!this.app.bY._.items.hasOwnProperty(K)) continue;
                                    var L = r.bY._.items[K];
                                    if (!L.mp[0].basketToolbar) continue;
                                    L = r.bY.create(K);
                                    var M = L.er(r, J),
                                    N = G[H].items.push(M) - 1;
                                    if (N > 0) {
                                        M.previous = G[H].items[N - 1];
                                        M.previous.next = M;
                                    }
                                    if (!y[H]) y[H] = [];
                                    y[H].push(N);
                                }
                                J.push('</span>');
                                I.appendHtml(J.join(''));
                            }
                            this.on('beforeShowFolderFiles',
                            function(P) {
                                this.app.document.getById('basket').remove();
                                var Q = this.app.dh.fk;
                                for (var R = 0; R < Q.length; R++) for (var S = 0; S < Q[R].items.length; S++) {
                                    if (y[R][S]) delete Q[R].items[S];
                                }
                                P.aF();
                            },
                            null, null, 1);
                            this.oW('successShowFolderFiles', F.data);
                            this.oW('afterShowFolderFiles', F.data);
                        });
                    }
                    function C(F) {
                        for (var G in F.plugins) {
                            if (!F.plugins.hasOwnProperty(G)) continue;
                            G = F.plugins[G];
                            if (!G.basketToolbar) continue;
                            for (var H = 0; H < G.basketToolbar.length; H++) {
                                var I = G.basketToolbar[H];
                                if (F.bY._.items[I[0]]) continue;
                                var J = i.deepCopy(I[1]);
                                if (!J.command) {
                                    var K = I[1].onClick,
                                    L = 'BasketToolbar_' + I[0];
                                    F.bD('BasketToolbar_' + I[0], {
                                        exec: function(M) {
                                            K(M.cg);
                                        }
                                    });
                                    J.command = L;
                                }
                                if (F.lang[J.label]) J.label = F.lang[J.label];
                                J.basketToolbar = 1;
                                F.bY.add(I[0], CKFinder._.UI_BUTTON, J);
                            }
                        }
                    };
                    function D(F) {
                        if (!F.eU) return;
                        for (var G in F.plugins) {
                            if (!F.plugins.hasOwnProperty(G)) continue;
                            G = F.plugins[G];
                            if (!G.basketFileContextMenu) continue;
                            for (var H = 0; H < G.basketFileContextMenu.length; H++) {
                                var I = G.basketFileContextMenu[H];
                                if (F._.iG[I[0]]) continue;
                                var J = i.deepCopy(I[1]);
                                if (!J.command) {
                                    var K = 'BasketContextMenu_' + I[0],
                                    L = I[1].onClick;
                                    F.bD('BasketContextMenu_' + I[0], {
                                        exec: function(M) {
                                            L(M.cg);
                                        }
                                    });
                                    J.command = K;
                                }
                                if (F.lang[J.label]) J.label = F.lang[J.label];
                                F.gp(I[0], J);
                                A.push(I[0]);
                            }
                        }
                    };
                });
            }
        });
        a.aL.BasketFolder = i.createClass({
            $: function(r) {
                var s = this;
                a.aL.Folder.call(s, r, null, r.lang.BasketFolder);
                s.hasChildren = 0;
                s.acl = new a.aL.Acl('1111111');
                s.isBasket = true;
            },
            base: a.aL.Folder,
            ej: {
                createNewFolder: function() {},
                getChildren: function(r) {
                    r.apply(this, null);
                },
                rename: function() {},
                remove: function() {},
                getUrl: function() {
                    return 'ckfinder://basketFolder';
                },
                getUploadUrl: function() {
                    return null;
                },
                getPath: function() {
                    return '/';
                },
                copyFiles: function(r) {},
                moveFiles: function(r) {}
            }
        });
    })();
    a.rs = 0;
    a.sz = 1;
    a.sy = 2;
    a.ss = 3;
    (function() {
        function p(P) {
            return ! !this._.tabs[P][0].$.offsetHeight;
        };
        function q() {
            var T = this;
            var P = T._.gx,
            Q = T._.cU.length,
            R = i.indexOf(T._.cU, P) + Q;
            for (var S = R - 1; S > R - Q; S--) {
                if (p.call(T, T._.cU[S % Q])) return T._.cU[S % Q];
            }
            return null;
        };
        function r() {
            var T = this;
            var P = T._.gx,
            Q = T._.cU.length,
            R = i.indexOf(T._.cU, P);
            for (var S = R + 1; S < R + Q; S++) {
                if (p.call(T, T._.cU[S % Q])) return T._.cU[S % Q];
            }
            return null;
        };
        a.dialog = function(P, Q) {
            var R = a.dialog._.ev[Q];
            R = i.extend(R(P), t);
            R = i.clone(R);
            R = new x(this, R);
            var S = a.document,
            T = P.theme.pu(P);
            this._ = {
                app: P,
                ax: T.ax,
                name: Q,
                hB: {
                    width: 0,
                    height: 0
                },
                size: {
                    width: 0,
                    height: 0
                },
                gH: false,
                contents: {},
                buttons: {},
                iX: {},
                tabs: {},
                cU: [],
                gx: null,
                nM: null,
                gV: 0,
                qF: null,
                eC: false,
                eO: [],
                gu: 0,
                hasFocus: false
            };
            this.bO = T.bO;
            this.bO.dialog.setStyles({
                position: f.ie6Compat ? 'absolute': 'fixed',
                top: 0,
                left: 0,
                visibility: 'hidden'
            });
            a.event.call(this);
            this.dg = R = a.oW('dialogDefinition', {
                name: Q,
                dg: R
            },
            P).dg;
            if (R.onLoad) this.on('load', R.onLoad);
            if (R.onShow) this.on('show', R.onShow);
            if (R.onHide) this.on('hide', R.onHide);
            if (R.onOk) this.on('ok',
            function(aD) {
                if (R.onOk.call(this, aD) === false) aD.data.hide = false;
            });
            if (R.onCancel) this.on('cancel',
            function(aD) {
                if (R.onCancel.call(this, aD) === false) aD.data.hide = false;
            });
            var U = this,
            V = function(aD) {
                var aP = U._.contents,
                bV = false;
                for (var eN in aP) for (var gB in aP[eN]) {
                    bV = aD.call(this, aP[eN][gB]);
                    if (bV) return;
                }
            };
            this.on('ok',
            function(aD) {
                V(function(aP) {
                    if (aP.validate) {
                        var bV = aP.validate(this);
                        if (typeof bV == 'string') {
                            P.document.getWindow().$.alert(bV);
                            bV = false;
                        }
                        if (bV === false) {
                            if (aP.select) aP.select();
                            else aP.focus();
                            aD.data.hide = false;
                            aD.stop();
                            return true;
                        }
                    }
                });
            },
            this, null, 0);
            this.on('cancel',
            function(aD) {
                V(function(aP) {
                    if (aP.isChanged()) {
                        if (!P.document.getWindow().$.confirm(P.lang.common.confirmCancel)) aD.data.hide = false;
                        return true;
                    }
                });
            },
            this, null, 0);
            this.bO.close.on('click',
            function(aD) {
                if (this.oW('cancel', {
                    hide: true
                }).hide !== false) this.hide();
            },
            this);
            function W(aD) {
                var aP = U._.eO,
                bV = aD ? 1 : -1;
                if (aP.length < 1) return;
                var eN = (U._.gu + bV + aP.length) % aP.length,
                gB = eN;
                while (!aP[gB].fM()) {
                    gB = (gB + bV + aP.length) % aP.length;
                    if (gB == eN) break;
                }
                aP[gB].focus();
                if (aP[gB].type == 'text') aP[gB].select();
            };
            var X;
            function Y(aD) {
                if (U != a.dialog._.dL) return;
                var aP = aD.data.db();
                X = 0;
                if (aP == 9 || aP == a.dy + 9) {
                    var bV = aP == a.dy + 9;
                    if (U._.eC) {
                        var eN = bV ? q.call(U) : r.call(U);
                        U.selectPage(eN);
                        U._.tabs[eN][0].focus();
                    } else W(!bV);
                    X = 1;
                } else if (aP == a.eJ + 121 && !U._.eC) {
                    U._.eC = true;
                    U._.tabs[U._.gx][0].focus();
                    X = 1;
                } else if ((aP == 37 || aP == 39) && U._.eC) {
                    eN = aP == 37 ? q.call(U) : r.call(U);
                    U.selectPage(eN);
                    U._.tabs[eN][0].focus();
                    X = 1;
                }
                if (X) {
                    aD.stop();
                    aD.data.preventDefault();
                }
            };
            function Z(aD) {
                X && aD.data.preventDefault();
            };
            this.on('show',
            function() {
                a.document.on('keydown', Y, this, null, 0);
                if (f.opera || f.gecko && f.mac) a.document.on('keypress', Z, this);
                if (f.ie6Compat) {
                    var aD = C.aC(0).getFrameDocument();
                    aD.on('keydown', Y, this, null, 0);
                }
            });
            this.on('hide',
            function() {
                a.document.aF('keydown', Y);
                if (f.opera || f.gecko && f.mac) a.document.aF('keypress', Z);
            });
            this.on('iframeAdded',
            function(aD) {
                var aP = new j(aD.data.iframe.$.contentWindow.document);
                aP.on('keydown', Y, this, null, 0);
            });
            this.on('show',
            function() {
                if (!this._.hasFocus) {
                    this._.gu = -1;
                    W(true);
                }
            },
            this, null, 4294967295);
            if (f.ie6Compat) this.on('load',
            function(aD) {
                var aP = this.getElement(),
                bV = aP.getFirst();
                bV.remove();
                bV.appendTo(aP);
            },
            this);
            z(this);
            A(this);
            this.bO.title.setText(R.title);
            for (var aa = 0; aa < R.contents.length; aa++) this.addPage(R.contents[aa]);
            var aT = /cke_dialog_tab(\s|$|_)/,
            bm = /cke_dialog_tab(\s|$)/;
            this.bO.tabs.on('click',
            function(aD) {
                var dX = this;
                var aP = aD.data.bK(),
                bV = aP,
                eN,
                gB;
                if (! (aT.test(aP.$.className) || aP.getName() == 'a')) return;
                eN = aP.$.id.substr(0, aP.$.id.lastIndexOf('_'));
                dX.selectPage(eN);
                if (dX._.eC) {
                    dX._.eC = false;
                    dX._.gu = -1;
                    W(true);
                }
                aD.data.preventDefault();
            },
            this);
            var aA = [],
            au = a.dialog._.gv.hbox.dQ(this, {
                type: 'hbox',
                className: 'cke_dialog_footer_buttons',
                widths: [],
                children: R.buttons
            },
            aA).aC();
            this.bO.footer.setHtml(aA.join(''));
            for (aa = 0; aa < au.length; aa++) this._.buttons[au[aa].id] = au[aa];
            a.skins.load(P, 'dialog');
        };
        function s(P, Q, R) {
            this.ax = Q;
            this.cQ = R;
            this.fM = function() {
                return ! Q.getAttribute('disabled') && Q.isVisible();
            };
            this.focus = function() {
                P._.gu = this.cQ;
                this.ax.focus();
            };
            Q.on('keydown',
            function(S) {
                if (S.data.db() in {
                    32 : 1,
                    13 : 1
                }) this.oW('click');
            });
            Q.on('focus',
            function() {
                this.oW('mouseover');
            });
            Q.on('blur',
            function() {
                this.oW('mouseout');
            });
        };
        a.dialog.prototype = {
            resize: (function() {
                return function(P, Q) {
                    var R = this;
                    if (R._.hB && R._.hB.width == P && R._.hB.height == Q) return;
                    a.dialog.oW('resize', {
                        dialog: R,
                        skin: R._.app.gd,
                        width: P,
                        height: Q
                    },
                    R._.app);
                    R._.hB = {
                        width: P,
                        height: Q
                    };
                    R._.gH = true;
                };
            })(),
            hR: function() {
                var R = this;
                if (!R._.gH) return R._.size;
                var P = R._.ax.getFirst(),
                Q = R._.size = {
                    width: P.$.offsetWidth || 0,
                    height: P.$.offsetHeight || 0
                };
                R._.gH = !Q.width || !Q.height;
                return Q;
            },
            mn: function() {
                var P = this.hR();
                P.height = P.height - (this.bO.title.$.offsetHeight || 0) - (this.bO.footer.$.offsetHeight || 0);
                return P;
            },
            move: (function() {
                var P;
                return function(Q, R) {
                    var U = this;
                    var S = U._.ax.getFirst();
                    if (P === undefined) P = S.getComputedStyle('position') == 'fixed';
                    if (P && U._.position && U._.position.x == Q && U._.position.y == R) return;
                    U._.position = {
                        x: Q,
                        y: R
                    };
                    if (!P) {
                        var T = a.document.getWindow().hV();
                        Q += T.x;
                        R += T.y;
                    }
                    S.setStyles({
                        left: (Q > 0 ? Q: 0) + 'px',
                        top: (R > 0 ? R: 0) + 'px'
                    });
                };
            })(),
            gz: function() {
                return i.extend({},
                this._.position);
            },
            show: function() {
                var P = this._.app;
                if (P.mode == 'qt' && g) {
                    var Q = P.getSelection();
                    Q && Q.up();
                }
                var R = this._.ax,
                S = this.dg;
                if (! (R.getParent() && R.getParent().equals(a.document.bH()))) R.appendTo(a.document.bH());
                else return;
                if (f.gecko && f.version < 10900) {
                    var T = this.bO.dialog;
                    T.setStyle('position', 'absolute');
                    setTimeout(function() {
                        T.setStyle('position', 'fixed');
                    },
                    0);
                }
                this.resize(S.minWidth, S.minHeight);
                this.selectPage(this.dg.contents[0].id);
                this.reset();
                if (a.dialog._.gw === null) a.dialog._.gw = this._.app.config.baseFloatZIndex;
                this._.ax.getFirst().setStyle('z-index', a.dialog._.gw += 10);
                if (a.dialog._.dL === null) {
                    a.dialog._.dL = this;
                    this._.ep = null;
                    D(this._.app);
                    R.on('keydown', G);
                    R.on(f.opera ? 'keypress': 'keyup', H);
                    for (var U in {
                        keyup: 1,
                        keydown: 1,
                        keypress: 1
                    }) R.on(U, N);
                } else {
                    this._.ep = a.dialog._.dL;
                    var V = this._.ep.getElement().getFirst();
                    V.$.style.zIndex -= Math.floor(this._.app.config.baseFloatZIndex / 2);
                    a.dialog._.dL = this;
                }
                I(this, this, '\x1b', null,
                function() {
                    this.getButton('cancel') && this.getButton('cancel').click();
                });
                this._.hasFocus = false;
                i.setTimeout(function() {
                    var W = a.document.getWindow().eR(),
                    X = this.hR();
                    this.move((W.width - S.minWidth) / 2, (W.height - X.height) / 2);
                    this.bO.dialog.setStyle('visibility', '');
                    this.cr('load', {});
                    this.oW('show', {});
                    this._.app.oW('dialogShow', this);
                    this.gh(function(Y) {
                        Y.jW && Y.jW();
                    });
                },
                100, this);
            },
            gh: function(P) {
                var S = this;
                for (var Q in S._.contents) for (var R in S._.contents[Q]) P(S._.contents[Q][R]);
                return S;
            },
            reset: (function() {
                var P = function(Q) {
                    if (Q.reset) Q.reset();
                };
                return function() {
                    this.gh(P);
                    return this;
                };
            })(),
            rN: function() {
                var P = arguments;
                this.gh(function(Q) {
                    if (Q.qi) Q.qi.apply(Q, P);
                });
            },
            sI: function() {
                var P = arguments;
                this.gh(function(Q) {
                    if (Q.rx) Q.rx.apply(Q, P);
                });
            },
            hide: function() {
                this.oW('hide', {});
                this._.app.oW('dialogHide', this);
                var P = this._.ax;
                if (!P.getParent()) return;
                P.remove();
                this.bO.dialog.setStyle('visibility', 'hidden');
                J(this);
                if (!this._.ep) E();
                else {
                    var Q = this._.ep.getElement().getFirst();
                    Q.setStyle('z-index', parseInt(Q.$.style.zIndex, 10) + Math.floor(this._.app.config.baseFloatZIndex / 2));
                }
                a.dialog._.dL = this._.ep;
                if (!this._.ep) {
                    a.dialog._.gw = null;
                    P.aF('keydown', G);
                    P.aF(f.opera ? 'keypress': 'keyup', H);
                    for (var R in {
                        keyup: 1,
                        keydown: 1,
                        keypress: 1
                    }) P.aF(R, N);
                    var S = this._.app;
                    S.focus();
                    if (S.mode == 'qt' && g) {
                        var T = S.getSelection();
                        T && T.sd(true);
                    }
                } else a.dialog._.gw -= 10;
                this.gh(function(U) {
                    U.ki && U.ki();
                });
            },
            addPage: function(P) {
                var Z = this;
                var Q = [],
                R = P.label ? ' title="' + i.htmlEncode(P.label) + '"': '',
                S = P.elements,
                T = a.dialog._.gv.vbox.dQ(Z, {
                    type: 'vbox',
                    className: 'cke_dialog_page_contents',
                    children: P.elements,
                    expand: !!P.expand,
                    padding: P.padding,
                    style: P.style || 'width: 100%; height: 100%;'
                },
                Q),
                U = k.et(Q.join(''), a.document),
                V = k.et(['<a class="cke_dialog_tab"', Z._.gV > 0 ? ' cke_last': 'cke_first', R, !!P.hidden ? ' style="display:none"': '', ' id="', P.id + '_', i.getNextNumber(), '" href="javascript:void(0)"', ' hp="true">', P.label, '</a>'].join(''), a.document);
                if (Z._.gV === 0) Z.bO.dialog.addClass('cke_single_page');
                else Z.bO.dialog.removeClass('cke_single_page');
                Z._.tabs[P.id] = [V, U];
                Z._.cU.push(P.id);
                Z._.gV++;
                Z._.qF = V;
                var W = Z._.contents[P.id] = {},
                X,
                Y = T.aC();
                while (X = Y.shift()) {
                    W[X.id] = X;
                    if (typeof X.aC == 'function') Y.push.apply(Y, X.aC());
                }
                U.setAttribute('name', P.id);
                U.appendTo(Z.bO.contents);
                V.unselectable();
                Z.bO.tabs.append(V);
                if (P.accessKey) {
                    I(Z, Z, 'bP+' + P.accessKey, L, K);
                    Z._.iX['bP+' + P.accessKey] = P.id;
                }
            },
            selectPage: function(P) {
                var U = this;
                for (var Q in U._.tabs) {
                    var R = U._.tabs[Q][0],
                    S = U._.tabs[Q][1];
                    if (Q != P) {
                        R.removeClass('cke_dialog_tab_selected');
                        S.hide();
                    }
                }
                var T = U._.tabs[P];
                T[0].addClass('cke_dialog_tab_selected');
                T[1].show();
                U._.gx = P;
                U._.nM = i.indexOf(U._.cU, P);
            },
            vJ: function(P) {
                var Q = this._.tabs[P] && this._.tabs[P][0];
                if (!Q) return;
                Q.hide();
            },
            showPage: function(P) {
                var Q = this._.tabs[P] && this._.tabs[P][0];
                if (!Q) return;
                Q.show();
            },
            getElement: function() {
                return this._.ax;
            },
            getName: function() {
                return this._.name;
            },
            getContentElement: function(P, Q) {
                return this._.contents[P][Q];
            },
            getValueOf: function(P, Q) {
                return this.getContentElement(P, Q).getValue();
            },
            setValueOf: function(P, Q, R) {
                return this.getContentElement(P, Q).setValue(R);
            },
            getButton: function(P) {
                return this._.buttons[P];
            },
            click: function(P) {
                return this._.buttons[P].click();
            },
            disableButton: function(P) {
                return this._.buttons[P].disable();
            },
            enableButton: function(P) {
                return this._.buttons[P].enable();
            },
            vj: function() {
                return this._.gV;
            },
            getParentApi: function() {
                return this._.app.cg;
            },
            eY: function() {
                return this._.app;
            },
            rf: function() {
                return this.eY().getSelection().rf();
            },
            tQ: function(P, Q) {
                var S = this;
                if (typeof Q == 'undefined') {
                    Q = S._.eO.length;
                    S._.eO.push(new s(S, P, Q));
                } else {
                    S._.eO.splice(Q, 0, new s(S, P, Q));
                    for (var R = Q + 1; R < S._.eO.length; R++) S._.eO[R].cQ++;
                }
            },
            setTitle: function(P) {
                this.bO.title.setText(P);
            }
        };
        i.extend(a.dialog, {
            add: function(P, Q) {
                if (!this._.ev[P] || typeof Q == 'function') this._.ev[P] = Q;
            },
            exists: function(P) {
                return ! !this._.ev[P];
            },
            getCurrent: function() {
                return a.dialog._.dL;
            },
            okButton: (function() {
                var P = function(Q, R) {
                    R = R || {};
                    return i.extend({
                        id: 'ok',
                        type: 'button',
                        label: Q.lang.common.ok,
                        'class': 'cke_dialog_ui_button_ok',
                        onClick: function(S) {
                            var T = S.data.dialog;
                            if (T.oW('ok', {
                                hide: true
                            }).hide !== false) T.hide();
                        }
                    },
                    R, true);
                };
                P.type = 'button';
                P.override = function(Q) {
                    return i.extend(function(R) {
                        return P(R, Q);
                    },
                    {
                        type: 'button'
                    },
                    true);
                };
                return P;
            })(),
            cancelButton: (function() {
                var P = function(Q, R) {
                    R = R || {};
                    return i.extend({
                        id: 'cancel',
                        type: 'button',
                        label: Q.lang.common.cancel,
                        'class': 'cke_dialog_ui_button_cancel',
                        onClick: function(S) {
                            var T = S.data.dialog;
                            if (T.oW('cancel', {
                                hide: true
                            }).hide !== false) T.hide();
                        }
                    },
                    R, true);
                };
                P.type = 'button';
                P.override = function(Q) {
                    return i.extend(function(R) {
                        return P(R, Q);
                    },
                    {
                        type: 'button'
                    },
                    true);
                };
                return P;
            })(),
            addUIElement: function(P, Q) {
                this._.gv[P] = Q;
            }
        });
        a.dialog._ = {
            gv: {},
            ev: {},
            dL: null,
            gw: null
        };
        a.event.du(a.dialog);
        a.event.du(a.dialog.prototype, true);
        var t = {
            jy: a.rs,
            minWidth: 600,
            minHeight: 400,
            buttons: [a.dialog.okButton, a.dialog.cancelButton]
        },
        u = function(P, Q, R) {
            for (var S = 0, T; T = P[S]; S++) {
                if (T.id == Q) return T;
                if (R && T[R]) {
                    var U = u(T[R], Q, R);
                    if (U) return U;
                }
            }
            return null;
        },
        v = function(P, Q, R, S, T) {
            if (R) {
                for (var U = 0, V; V = P[U]; U++) {
                    if (V.id == R) {
                        P.splice(U, 0, Q);
                        return Q;
                    }
                    if (S && V[S]) {
                        var W = v(V[S], Q, R, S, true);
                        if (W) return W;
                    }
                }
                if (T) return null;
            }
            P.push(Q);
            return Q;
        },
        w = function(P, Q, R) {
            for (var S = 0, T; T = P[S]; S++) {
                if (T.id == Q) return P.splice(S, 1);
                if (R && T[R]) {
                    var U = w(T[R], Q, R);
                    if (U) return U;
                }
            }
            return null;
        },
        x = function(P, Q) {
            this.dialog = P;
            var R = Q.contents;
            for (var S = 0, T; T = R[S]; S++) R[S] = new y(P, T);
            i.extend(this, Q);
        };
        x.prototype = {
            vz: function(P) {
                return u(this.contents, P);
            },
            getButton: function(P) {
                return u(this.buttons, P);
            },
            uh: function(P, Q) {
                return v(this.contents, P, Q);
            },
            qW: function(P, Q) {
                return v(this.buttons, P, Q);
            },
            uP: function(P) {
                w(this.contents, P);
            },
            uO: function(P) {
                w(this.buttons, P);
            }
        };
        function y(P, Q) {
            this._ = {
                dialog: P
            };
            i.extend(this, Q);
        };
        y.prototype = {
            eB: function(P) {
                return u(this.elements, P, 'children');
            },
            add: function(P, Q) {
                return v(this.elements, P, Q, 'children');
            },
            remove: function(P) {
                w(this.elements, P, 'children');
            }
        };
        function z(P) {
            var Q = null,
            R = null,
            S = P.getElement().getFirst(),
            T = P.eY(),
            U = T.config.dialog_magnetDistance,
            V = T.skin.margins || [0, 0, 0, 0];
            if (typeof U == 'undefined') U = 20;
            function W(Y) {
                var Z = P.hR(),
                aa = a.document.getWindow().eR(),
                aT = Y.data.$.screenX,
                bm = Y.data.$.screenY,
                aA = aT - Q.x,
                au = bm - Q.y,
                aD,
                aP;
                Q = {
                    x: aT,
                    y: bm
                };
                R.x += aA;
                R.y += au;
                if (R.x + V[3] < U) aD = -V[3];
                else if (R.x - V[1] > aa.width - Z.width - U) aD = aa.width - Z.width + V[1];
                else aD = R.x;
                if (R.y + V[0] < U) aP = -V[0];
                else if (R.y - V[2] > aa.height - Z.height - U) aP = aa.height - Z.height + V[2];
                else aP = R.y;
                P.move(aD, aP);
                Y.data.preventDefault();
            };
            function X(Y) {
                a.document.aF('mousemove', W);
                a.document.aF('mouseup', X);
                if (f.ie6Compat) {
                    var Z = C.aC(0).getFrameDocument();
                    Z.aF('mousemove', W);
                    Z.aF('mouseup', X);
                }
            };
            P.bO.title.on('mousedown',
            function(Y) {
                P._.gH = true;
                Q = {
                    x: Y.data.$.screenX,
                    y: Y.data.$.screenY
                };
                a.document.on('mousemove', W);
                a.document.on('mouseup', X);
                R = P.gz();
                if (f.ie6Compat) {
                    var Z = C.aC(0).getFrameDocument();
                    Z.on('mousemove', W);
                    Z.on('mouseup', X);
                }
                Y.data.preventDefault();
            },
            P);
        };
        function A(P) {
            var Q = P.dg,
            R = Q.minWidth || 0,
            S = Q.minHeight || 0,
            T = Q.jy,
            U = P.eY().skin.margins || [0, 0, 0, 0];
            function V(aP, bV) {
                aP.y += bV;
            };
            function W(aP, bV) {
                aP.eS += bV;
            };
            function X(aP, bV) {
                aP.bW += bV;
            };
            function Y(aP, bV) {
                aP.x += bV;
            };
            var Z = null,
            aa = null,
            aT = P._.app.config.ux,
            bm = ['tl', 't', 'tr', 'l', 'r', 'bl', 'b', 'br'];
            function aA(aP) {
                var bV = aP.jO.fU,
                eN = P.hR();
                aa = P.gz();
                i.extend(aa, {
                    eS: aa.x + eN.width,
                    bW: aa.y + eN.height
                });
                Z = {
                    x: aP.data.$.screenX,
                    y: aP.data.$.screenY
                };
                a.document.on('mousemove', au, P, {
                    fU: bV
                });
                a.document.on('mouseup', aD, P, {
                    fU: bV
                });
                if (f.ie6Compat) {
                    var gB = C.aC(0).getFrameDocument();
                    gB.on('mousemove', au, P, {
                        fU: bV
                    });
                    gB.on('mouseup', aD, P, {
                        fU: bV
                    });
                }
                aP.data.preventDefault();
            };
            function au(aP) {
                var bV = aP.data.$.screenX,
                eN = aP.data.$.screenY,
                gB = bV - Z.x,
                dX = eN - Z.y,
                gs = a.document.getWindow().eR(),
                am = aP.jO.fU;
                if (am.search('t') != -1) V(aa, dX);
                if (am.search('l') != -1) Y(aa, gB);
                if (am.search('b') != -1) X(aa, dX);
                if (am.search('r') != -1) W(aa, gB);
                Z = {
                    x: bV,
                    y: eN
                };
                var gP, gR, pw, aq;
                if (aa.x + U[3] < aT) gP = -U[3];
                else if (am.search('l') != -1 && aa.eS - aa.x < R + aT) gP = aa.eS - R;
                else gP = aa.x;
                if (aa.y + U[0] < aT) gR = -U[0];
                else if (am.search('t') != -1 && aa.bW - aa.y < S + aT) gR = aa.bW - S;
                else gR = aa.y;
                if (aa.eS - U[1] > gs.width - aT) pw = gs.width + U[1];
                else if (am.search('r') != -1 && aa.eS - aa.x < R + aT) pw = aa.x + R;
                else pw = aa.eS;
                if (aa.bW - U[2] > gs.height - aT) aq = gs.height + U[2];
                else if (am.search('b') != -1 && aa.bW - aa.y < S + aT) aq = aa.y + S;
                else aq = aa.bW;
                P.move(gP, gR);
                P.resize(pw - gP, aq - gR);
                aP.data.preventDefault();
            };
            function aD(aP) {
                a.document.aF('mouseup', aD);
                a.document.aF('mousemove', au);
                if (f.ie6Compat) {
                    var bV = C.aC(0).getFrameDocument();
                    bV.aF('mouseup', aD);
                    bV.aF('mousemove', au);
                }
            };
        };
        var B, C, D = function(P) {
            var Q = a.document.getWindow();
            if (!C) {
                var R = P.config.so || 'white',
                S = ['<div style="position: ', f.ie6Compat ? 'absolute': 'fixed', '; z-index: ', P.config.baseFloatZIndex, '; top: 0px; left: 0px; ', !f.ie6Compat ? 'background-color: ' + R: '', '" id="cke_dialog_background_cover">'];
                if (f.ie6Compat) {
                    var T = f.isCustomDomain(),
                    U = "<html><body style=\\'background-color:" + R + ";\\'></body></html>";
                    S.push('<iframe hp="true" frameborder="0" id="cke_dialog_background_iframe" src="javascript:');
                    S.push('void((function(){document.open();' + (T ? "document.domain='" + document.domain + "';": '') + "document.write( '" + U + "' );" + 'document.close();' + '})())');
                    S.push('" style="position:absolute;left:0;top:0;width:100%;height: 100%;progid:DXImageTransform.Microsoft.Alpha(opacity=0)"></iframe>');
                }
                S.push('</div>');
                C = k.et(S.join(''), a.document);
            }
            var V = C,
            W = function() {
                var aa = Q.eR();
                V.setStyles({
                    width: aa.width + 'px',
                    height: aa.height + 'px'
                });
            },
            X = function() {
                var aa = Q.hV(),
                aT = a.dialog._.dL;
                V.setStyles({
                    left: aa.x + 'px',
                    top: aa.y + 'px'
                });
                do {
                    var bm = aT.gz();
                    aT.move(bm.x, bm.y);
                } while ( aT = aT . _ . ep );
            };
            B = W;
            Q.on('resize', W);
            W();
            if (f.ie6Compat) {
                var Y = function() {
                    X();
                    arguments.callee.lw.apply(this, arguments);
                };
                Q.$.setTimeout(function() {
                    Y.lw = window.onscroll || (function() {});
                    window.onscroll = Y;
                },
                0);
                X();
            }
            var Z = P.config.dialog_backgroundCoverOpacity;
            V.setOpacity(typeof Z != 'undefined' ? Z: 0.5);
            V.appendTo(a.document.bH());
        },
        E = function() {
            if (!C) return;
            var P = a.document.getWindow();
            C.remove();
            P.aF('resize', B);
            if (f.ie6Compat) P.$.setTimeout(function() {
                var Q = window.onscroll && window.onscroll.lw;
                window.onscroll = Q || null;
            },
            0);
            B = null;
        },
        F = {},
        G = function(P) {
            var Q = P.data.$.ctrlKey || P.data.$.metaKey,
            R = P.data.$.altKey,
            S = P.data.$.shiftKey,
            T = String.fromCharCode(P.data.$.keyCode),
            U = F[(Q ? 'bP+': '') + (R ? 'eJ+': '') + (S ? 'dy+': '') + T];
            if (!U || !U.length) return;
            U = U[U.length - 1];
            U.keydown && U.keydown.call(U.bf, U.dialog, U.iK);
            P.data.preventDefault();
        },
        H = function(P) {
            var Q = P.data.$.ctrlKey || P.data.$.metaKey,
            R = P.data.$.altKey,
            S = P.data.$.shiftKey,
            T = String.fromCharCode(P.data.$.keyCode),
            U = F[(Q ? 'bP+': '') + (R ? 'eJ+': '') + (S ? 'dy+': '') + T];
            if (!U || !U.length) return;
            U = U[U.length - 1];
            if (U.keyup) {
                U.keyup.call(U.bf, U.dialog, U.iK);
                P.data.preventDefault();
            }
        },
        I = function(P, Q, R, S, T) {
            var U = F[R] || (F[R] = []);
            U.push({
                bf: P,
                dialog: Q,
                iK: R,
                keyup: T || P.eZ,
                keydown: S || P.iU
            });
        },
        J = function(P) {
            for (var Q in F) {
                var R = F[Q];
                for (var S = R.length - 1; S >= 0; S--) {
                    if (R[S].dialog == P || R[S].bf == P) R.splice(S, 1);
                }
                if (R.length === 0) delete F[Q];
            }
        },
        K = function(P, Q) {
            if (P._.iX[Q]) P.selectPage(P._.iX[Q]);
        },
        L = function(P, Q) {},
        M = {
            27 : 1,
            13 : 1
        },
        N = function(P) {
            if (P.data.db() in M) P.data.stopPropagation();
        };
        (function() {
            n.dialog = {
                bf: function(P, Q, R, S, T, U, V) {
                    if (arguments.length < 4) return;
                    var W = (S.call ? S(Q) : S) || 'div',
                    X = ['<', W, ' '],
                    Y = (T && T.call ? T(Q) : T) || {},
                    Z = (U && U.call ? U(Q) : U) || {},
                    aa = (V && V.call ? V(P, Q) : V) || '',
                    aT = this.oJ = Z.id || i.getNextNumber() + '_uiElement',
                    bm = this.id = Q.id,
                    aA;
                    Z.id = aT;
                    var au = {};
                    if (Q.type) au['cke_dialog_ui_' + Q.type] = 1;
                    if (Q.className) au[Q.className] = 1;
                    var aD = Z['class'] && Z['class'].split ? Z['class'].split(' ') : [];
                    for (aA = 0; aA < aD.length; aA++) {
                        if (aD[aA]) au[aD[aA]] = 1;
                    }
                    var aP = [];
                    for (aA in au) aP.push(aA);
                    Z['class'] = aP.join(' ');
                    if (Q.title) Z.title = Q.title;
                    var bV = (Q.style || '').split(';');
                    for (aA in Y) bV.push(aA + ':' + Y[aA]);
                    if (Q.hidden) bV.push('display:none');
                    for (aA = bV.length - 1; aA >= 0; aA--) {
                        if (bV[aA] === '') bV.splice(aA, 1);
                    }
                    if (bV.length > 0) Z.style = (Z.style ? Z.style + '; ': '') + bV.join('; ');
                    for (aA in Z) X.push(aA + '="' + i.htmlEncode(Z[aA]) + '" ');
                    X.push('>', aa, '</', W, '>');
                    R.push(X.join(''));
                    (this._ || (this._ = {})).dialog = P;
                    if (typeof Q.isChanged == 'boolean') this.isChanged = function() {
                        return Q.isChanged;
                    };
                    if (typeof Q.isChanged == 'function') this.isChanged = Q.isChanged;
                    a.event.du(this);
                    this.nc(Q);
                    if (this.eZ && this.iU && Q.accessKey) I(this, P, 'bP+' + Q.accessKey);
                    var eN = this;
                    P.on('load',
                    function() {
                        if (eN.getInputElement()) eN.getInputElement().on('focus',
                        function() {
                            P._.eC = false;
                            P._.hasFocus = true;
                            eN.oW('focus');
                        },
                        eN);
                    });
                    if (this.eA) {
                        this.cQ = P._.eO.push(this) - 1;
                        this.on('focus',
                        function() {
                            P._.gu = eN.cQ;
                        });
                    }
                    i.extend(this, Q);
                },
                hbox: function(P, Q, R, S, T) {
                    if (arguments.length < 4) return;
                    this._ || (this._ = {});
                    var U = this._.children = Q,
                    V = T && T.widths || null,
                    W = T && T.height || null,
                    X = {},
                    Y, Z = function() {
                        var aa = ['<tbody><tr class="cke_dialog_ui_hbox">'];
                        for (Y = 0; Y < R.length; Y++) {
                            var aT = 'cke_dialog_ui_hbox_child',
                            bm = [];
                            if (Y === 0) aT = 'cke_dialog_ui_hbox_first';
                            if (Y == R.length - 1) aT = 'cke_dialog_ui_hbox_last';
                            aa.push('<td class="', aT, '" ');
                            if (V) {
                                if (V[Y]) bm.push('width:' + i.cssLength(V[Y]));
                            } else bm.push('width:' + Math.floor(100 / R.length) + '%');
                            if (W) bm.push('height:' + i.cssLength(W));
                            if (T && T.padding != undefined) bm.push('padding:' + i.cssLength(T.padding));
                            if (bm.length > 0) aa.push('style="' + bm.join('; ') + '" ');
                            aa.push('>', R[Y], '</td>');
                        }
                        aa.push('</tr></tbody>');
                        return aa.join('');
                    };
                    n.dialog.bf.call(this, P, T || {
                        type: 'hbox'
                    },
                    S, 'table', X, T && T.align && {
                        align: T.align
                    } || null, Z);
                },
                vbox: function(P, Q, R, S, T) {
                    if (arguments.length < 3) return;
                    this._ || (this._ = {});
                    var U = this._.children = Q,
                    V = T && T.width || null,
                    W = T && T.vY || null,
                    X = function() {
                        var Y = ['<table cellspacing="0" border="0" '];
                        Y.push('style="');
                        if (T && T.expand) Y.push('height:100%;');
                        Y.push('width:' + i.cssLength(V || '100%'), ';');
                        Y.push('"');
                        Y.push('align="', i.htmlEncode(T && T.align || (P.eY().lang.dir == 'ltr' ? 'left': 'right')), '" ');
                        Y.push('><tbody>');
                        for (var Z = 0; Z < R.length; Z++) {
                            var aa = [];
                            Y.push('<tr><td ');
                            if (V) aa.push('width:' + i.cssLength(V || '100%'));
                            if (W) aa.push('height:' + i.cssLength(W[Z]));
                            else if (T && T.expand) aa.push('height:' + Math.floor(100 / R.length) + '%');
                            if (T && T.padding != undefined) aa.push('padding:' + i.cssLength(T.padding));
                            if (aa.length > 0) Y.push('style="', aa.join('; '), '" ');
                            Y.push(' class="cke_dialog_ui_vbox_child">', R[Z], '</td></tr>');
                        }
                        Y.push('</tbody></table>');
                        return Y.join('');
                    };
                    n.dialog.bf.call(this, P, T || {
                        type: 'vbox'
                    },
                    S, 'div', null, null, X);
                }
            };
        })();
        n.dialog.bf.prototype = {
            getElement: function() {
                return a.document.getById(this.oJ);
            },
            getInputElement: function() {
                return this.getElement();
            },
            getDialog: function() {
                return this._.dialog;
            },
            setValue: function(P) {
                this.getInputElement().setValue(P);
                this.oW('change', {
                    value: P
                });
                return this;
            },
            getValue: function() {
                return this.getInputElement().getValue();
            },
            isChanged: function() {
                return false;
            },
            selectParentTab: function() {
                var S = this;
                var P = S.getInputElement(),
                Q = P,
                R;
                while ((Q = Q.getParent()) && Q.$.className.search('cke_dialog_page_contents') == -1) {}
                if (!Q) return S;
                R = Q.getAttribute('name');
                if (S._.dialog._.gx != R) S._.dialog.selectPage(R);
                return S;
            },
            focus: function() {
                this.selectParentTab().getInputElement().focus();
                return this;
            },
            nc: function(P) {
                var Q = /^on([A-Z]\w+)/,
                R, S = function(U, V, W, X) {
                    V.on('load',
                    function() {
                        U.getInputElement().on(W, X, U);
                    });
                };
                for (var T in P) {
                    if (! (R = T.match(Q))) continue;
                    if (this.dm[T]) this.dm[T].call(this, this._.dialog, P[T]);
                    else S(this, this._.dialog, R[1].toLowerCase(), P[T]);
                }
                return this;
            },
            dm: {
                onLoad: function(P, Q) {
                    P.on('load', Q, this);
                },
                onShow: function(P, Q) {
                    P.on('show', Q, this);
                },
                onHide: function(P, Q) {
                    P.on('hide', Q, this);
                }
            },
            iU: function(P, Q) {
                this.focus();
            },
            eZ: function(P, Q) {},
            disable: function() {
                var P = this.getInputElement();
                P.setAttribute('disabled', 'true');
                P.addClass('cke_disabled');
            },
            enable: function() {
                var P = this.getInputElement();
                P.removeAttribute('disabled');
                P.removeClass('cke_disabled');
            },
            isEnabled: function() {
                return ! this.getInputElement().getAttribute('disabled');
            },
            isVisible: function() {
                return this.getInputElement().isVisible();
            },
            fM: function() {
                if (!this.isEnabled() || !this.isVisible()) return false;
                return true;
            }
        };
        n.dialog.hbox.prototype = i.extend(new n.dialog.bf(), {
            aC: function(P) {
                var Q = this;
                if (arguments.length < 1) return Q._.children.concat();
                if (!P.splice) P = [P];
                if (P.length < 2) return Q._.children[P[0]];
                else return Q._.children[P[0]] && Q._.children[P[0]].aC ? Q._.children[P[0]].aC(P.slice(1, P.length)) : null;
            }
        },
        true);
        n.dialog.vbox.prototype = new n.dialog.hbox();
        (function() {
            var P = {
                dQ: function(Q, R, S) {
                    var T = R.children,
                    U, V = [],
                    W = [];
                    for (var X = 0; X < T.length && (U = T[X]); X++) {
                        var Y = [];
                        V.push(Y);
                        W.push(a.dialog._.gv[U.type].dQ(Q, U, Y));
                    }
                    return new n.dialog[R.type](Q, W, V, S, R);
                }
            };
            a.dialog.addUIElement('hbox', P);
            a.dialog.addUIElement('vbox', P);
        })();
        a.rB = function(P) {
            this.ry = P;
        };
        a.rB.prototype = {
            exec: function(P) {
                P.openDialog(this.ry);
            },
            sG: false
        };
        (function() {
            var P = /^([a]|[^a])+$/,
            Q = /^\d*$/,
            R = /^\d*(?:\.\d+)?$/;
            a.sg = 1;
            a.jb = 2;
            a.dialog.validate = {
                functions: function() {
                    return function() {
                        var Y = this;
                        var S = Y && Y.getValue ? Y.getValue() : arguments[0],
                        T = undefined,
                        U = a.jb,
                        V = [],
                        W;
                        for (W = 0; W < arguments.length; W++) {
                            if (typeof arguments[W] == 'function') V.push(arguments[W]);
                            else break;
                        }
                        if (W < arguments.length && typeof arguments[W] == 'string') {
                            T = arguments[W];
                            W++;
                        }
                        if (W < arguments.length && typeof arguments[W] == 'number') U = arguments[W];
                        var X = U == a.jb ? true: false;
                        for (W = 0; W < V.length; W++) {
                            if (U == a.jb) X = X && V[W](S);
                            else X = X || V[W](S);
                        }
                        if (!X) {
                            if (T !== undefined) alert(T);
                            if (Y && (Y.select || Y.focus)) Y.select || Y.focus();
                            return false;
                        }
                        return true;
                    };
                },
                regex: function(S, T) {
                    return function() {
                        var V = this;
                        var U = V && V.getValue ? V.getValue() : arguments[0];
                        if (!S.test(U)) {
                            if (T !== undefined) alert(T);
                            if (V && (V.select || V.focus)) if (V.select) V.select();
                            else V.focus();
                            return false;
                        }
                        return true;
                    };
                },
                notEmpty: function(S) {
                    return this.regex(P, S);
                },
                integer: function(S) {
                    return this.regex(Q, S);
                },
                number: function(S) {
                    return this.regex(R, S);
                },
                equals: function(S, T) {
                    return this.functions(function(U) {
                        return U == S;
                    },
                    T);
                },
                notEqual: function(S, T) {
                    return this.functions(function(U) {
                        return U != S;
                    },
                    T);
                }
            };
        })();
        function O(P, Q) {
            var R = function() {
                T(this);
                Q(this);
            },
            S = function() {
                T(this);
            },
            T = function(U) {
                U.aF('ok', R);
                U.aF('cancel', S);
            };
            P.on('ok', R);
            P.on('cancel', S);
        };
        i.extend(a.application.prototype, {
            openDialog: function(P, Q, R) {
                var S = a.dialog._.ev[P];
                if (typeof S == 'function') {
                    var T = this._.oB || (this._.oB = {}),
                    U = T[P] || (T[P] = new a.dialog(this, P));
                    Q && Q.call(U, U);
                    U.show();
                    return U;
                } else if (S == 'failed') throw new Error('[CKFINDER.dialog.openDialog] Dialog "' + P + '" failed when loading dg.');
                var V = a.document.bH(),
                W = V.$.style.cursor,
                X = this;
                V.setStyle('cursor', 'wait');
                a.ec.load(a.getUrl(S),
                function() {
                    if (typeof a.dialog._.ev[P] != 'function') a.dialog._.ev[P] = 'failed';
                    X.openDialog(P, Q);
                    V.setStyle('cursor', W);
                },
                null, null, R);
                return null;
            },
            hs: function(P, Q, R, S) {
                var T = this;
                setTimeout(function() {
                    T.cg.openDialog('Input',
                    function(U) {
                        U.show();
                        U.setTitle(P || T.lang.common.inputTitle);
                        U.getContentElement('tab1', 'msg').getElement().setHtml(Q);
                        U.getContentElement('tab1', 'input').setValue(R);
                        O(U,
                        function(W) {
                            var X = W.getContentElement('tab1', 'input').getValue();
                            S(X);
                        });
                        if (f.ie7Compat) {
                            var V = U.mn();
                            U.resize(V.width, V.height);
                        }
                    });
                },
                0);
            },
            msgDialog: function(P, Q, R) {
                var S = this;
                setTimeout(function() {
                    S.cg.openDialog('Msg',
                    function(T) {
                        T.show();
                        T.setTitle(P || S.lang.common.messageTitle);
                        T.getContentElement('tab1', 'msg').getElement().setHtml(Q);
                        R && O(T,
                        function(V) {
                            R();
                        });
                        if (f.ie7Compat) {
                            var U = T.mn();
                            T.resize(U.width, U.height);
                        }
                    });
                },
                0);
            },
            fe: function(P, Q, R) {
                var S = this;
                setTimeout(function() {
                    S.cg.openDialog('Confirm',
                    function(T) {
                        T.show();
                        T.setTitle(P || S.lang.common.confirmationTitle);
                        T.getContentElement('tab1', 'msg').getElement().setHtml(Q);
                        O(T,
                        function(V) {
                            R();
                        });
                        if (f.ie7Compat) {
                            var U = T.mn();
                            T.resize(U.width, U.height);
                        }
                    });
                },
                0);
            }
        });
        m.add('dialog', {
            bM: ['dialogui'],
            onLoad: function() {
                a.dialog.add('Confirm',
                function(P) {
                    return {
                        title: P.lang.common.confirmationTitle,
                        minWidth: 270,
                        minHeight: 60,
                        contents: [{
                            id: 'tab1',
                            elements: [{
                                type: 'html',
                                html: '',
                                id: 'msg'
                            }]
                        }],
                        buttons: [CKFinder.dialog.okButton, CKFinder.dialog.cancelButton]
                    };
                });
                a.dialog.add('Msg',
                function(P) {
                    return {
                        title: P.lang.common.messageTitle,
                        minWidth: 270,
                        minHeight: 60,
                        contents: [{
                            id: 'tab1',
                            elements: [{
                                type: 'html',
                                html: '',
                                id: 'msg'
                            }]
                        }],
                        buttons: [CKFinder.dialog.okButton]
                    };
                });
                a.dialog.add('Input',
                function(P) {
                    return {
                        title: P.lang.common.inputTitle,
                        minWidth: 270,
                        minHeight: 60,
                        contents: [{
                            id: 'tab1',
                            elements: [{
                                type: 'html',
                                html: '',
                                id: 'msg'
                            },
                            {
                                type: 'text',
                                id: 'input'
                            }]
                        }],
                        buttons: [CKFinder.dialog.okButton, CKFinder.dialog.cancelButton]
                    };
                });
            }
        });
    })();
    m.add('dialogui');
    (function() {
        var p = function(w) {
            var z = this;
            z._ || (z._ = {});
            z._['default'] = z._.hq = w['default'] || '';
            var x = [z._];
            for (var y = 1; y < arguments.length; y++) x.push(arguments[y]);
            x.push(true);
            i.extend.apply(i, x);
            return z._;
        },
        q = {
            dQ: function(w, x, y) {
                return new n.dialog.ju(w, x, y);
            }
        },
        r = {
            dQ: function(w, x, y) {
                return new n.dialog[x.type](w, x, y);
            }
        },
        s = {
            isChanged: function() {
                return this.getValue() != this.lu();
            },
            reset: function() {
                this.setValue(this.lu());
            },
            jW: function() {
                this._.hq = this.getValue();
            },
            ki: function() {
                this._.hq = this._['default'];
            },
            lu: function() {
                return this._.hq;
            }
        },
        t = i.extend({},
        n.dialog.bf.prototype.dm, {
            onChange: function(w, x) {
                if (!this._.pL) {
                    w.on('load',
                    function() {
                        this.getInputElement().on('change',
                        function() {
                            this.oW('change', {
                                value: this.getValue()
                            });
                        },
                        this);
                    },
                    this);
                    this._.pL = true;
                }
                this.on('change', x);
            }
        },
        true),
        u = /^on([A-Z]\w+)/,
        v = function(w) {
            for (var x in w) {
                if (u.test(x) || x == 'title' || x == 'type') delete w[x];
            }
            return w;
        };
        i.extend(n.dialog, {
            dD: function(w, x, y, z) {
                if (arguments.length < 4) return;
                var A = p.call(this, x);
                A.hz = i.getNextNumber() + '_label';
                var B = this._.children = [],
                C = function() {
                    var D = [];
                    if (x.uC != 'horizontal') D.push('<div class="cke_dialog_ui_labeled_label" id="', A.hz, '" >', x.label, '</div>', '<div class="cke_dialog_ui_labeled_content">', z(w, x), '</div>');
                    else {
                        var E = {
                            type: 'hbox',
                            widths: x.widths,
                            padding: 0,
                            children: [{
                                type: 'html',
                                html: '<span class="cke_dialog_ui_labeled_label" id="' + A.hz + '">' + i.htmlEncode(x.label) + '</span>'
                            },
                            {
                                type: 'html',
                                html: '<span class="cke_dialog_ui_labeled_content">' + z(w, x) + '</span>'
                            }]
                        };
                        a.dialog._.gv.hbox.dQ(w, E, D);
                    }
                    return D.join('');
                };
                n.dialog.bf.call(this, w, x, y, 'div', null, null, C);
            },
            ju: function(w, x, y) {
                if (arguments.length < 3) return;
                p.call(this, x);
                var z = this._.le = i.getNextNumber() + '_textInput',
                A = {
                    'class': 'cke_dialog_ui_input_' + x.type,
                    id: z,
                    type: 'text'
                },
                B;
                if (x.validate) this.validate = x.validate;
                if (x.maxLength) A.uy = x.maxLength;
                if (x.size) A.size = x.size;
                var C = this,
                D = false;
                w.on('load',
                function() {
                    C.getInputElement().on('keydown',
                    function(F) {
                        if (F.data.db() == 13) D = true;
                    });
                    C.getInputElement().on('keyup',
                    function(F) {
                        if (F.data.db() == 13 && D) {
                            w.getButton('ok') && setTimeout(function() {
                                w.getButton('ok').click();
                            },
                            0);
                            D = false;
                        }
                    },
                    null, null, 1000);
                });
                var E = function() {
                    var F = ['<div class="cke_dialog_ui_input_', x.type, '"'];
                    if (x.width) F.push('style="width:' + x.width + '" ');
                    F.push('><input ');
                    for (var G in A) F.push(G + '="' + A[G] + '" ');
                    F.push(' /></div>');
                    return F.join('');
                };
                n.dialog.dD.call(this, w, x, y, E);
            },
            textarea: function(w, x, y) {
                if (arguments.length < 3) return;
                p.call(this, x);
                var z = this,
                A = this._.le = i.getNextNumber() + '_textarea',
                B = {};
                if (x.validate) this.validate = x.validate;
                B.rows = x.rows || 5;
                B.cols = x.cols || 20;
                var C = function() {
                    var D = ['<div class="cke_dialog_ui_input_textarea"><textarea class="cke_dialog_ui_input_textarea" id="', A, '" '];
                    for (var E in B) D.push(E + '="' + i.htmlEncode(B[E]) + '" ');
                    D.push('>', i.htmlEncode(z._['default']), '</textarea></div>');
                    return D.join('');
                };
                n.dialog.dD.call(this, w, x, y, C);
            },
            checkbox: function(w, x, y) {
                if (arguments.length < 3) return;
                var z = p.call(this, x, {
                    'default': !!x['default']
                });
                if (x.validate) this.validate = x.validate;
                var A = function() {
                    var B = i.extend({},
                    x, {
                        id: x.id ? x.id + '_checkbox': i.getNextNumber() + '_checkbox'
                    },
                    true),
                    C = [],
                    D = {
                        'class': 'cke_dialog_ui_checkbox_input',
                        type: 'checkbox'
                    };
                    v(B);
                    if (x['default']) D.checked = 'checked';
                    z.checkbox = new n.dialog.bf(w, B, C, 'input', null, D);
                    C.push(' <label for="', D.id, '">', i.htmlEncode(x.label), '</label>');
                    return C.join('');
                };
                n.dialog.bf.call(this, w, x, y, 'span', null, null, A);
            },
            radio: function(w, x, y) {
                if (arguments.length < 3) return;
                p.call(this, x);
                if (!this._['default']) this._['default'] = this._.hq = x.items[0][1];
                if (x.validate) this.validate = x.sh;
                var z = [],
                A = this,
                B = function() {
                    var C = [],
                    D = [],
                    E = {
                        'class': 'cke_dialog_ui_radio_item'
                    },
                    F = x.id ? x.id + '_radio': i.getNextNumber() + '_radio';
                    for (var G = 0; G < x.items.length; G++) {
                        var H = x.items[G],
                        I = H[2] !== undefined ? H[2] : H[0],
                        J = H[1] !== undefined ? H[1] : H[0],
                        K = i.extend({},
                        x, {
                            id: i.getNextNumber() + '_radio_input',
                            title: null,
                            type: null
                        },
                        true),
                        L = i.extend({},
                        K, {
                            id: null,
                            title: I
                        },
                        true),
                        M = {
                            type: 'radio',
                            'class': 'cke_dialog_ui_radio_input',
                            name: F,
                            value: J
                        },
                        N = [];
                        if (A._['default'] == J) M.checked = 'checked';
                        v(K);
                        v(L);
                        z.push(new n.dialog.bf(w, K, N, 'input', null, M));
                        N.push(' ');
                        new n.dialog.bf(w, L, N, 'label', null, {
                            'for': M.id
                        },
                        H[0]);
                        C.push(N.join(''));
                    }
                    new n.dialog.hbox(w, [], C, D);
                    return D.join('');
                };
                n.dialog.dD.call(this, w, x, y, B);
                this._.children = z;
            },
            button: function(w, x, y) {
                if (!arguments.length) return;
                if (typeof x == 'function') x = x(w.eY());
                p.call(this, x, {
                    disabled: x.disabled || false
                });
                a.event.du(this);
                var z = this;
                w.on('load',
                function(B) {
                    var C = this.getElement();
                    (function() {
                        C.on('click',
                        function(D) {
                            z.oW('click', {
                                dialog: z.getDialog()
                            });
                            D.data.preventDefault();
                        });
                    })();
                    C.unselectable();
                },
                this);
                var A = i.extend({},
                x);
                delete A.style;
                n.dialog.bf.call(this, w, A, y, 'a', null, {
                    style: x.style,
                    href: 'javascript:void(0)',
                    title: x.label,
                    hp: 'true',
                    'class': x['class']
                },
                '<span class="cke_dialog_ui_button">' + i.htmlEncode(x.label) + '</span>');
            },
            select: function(w, x, y) {
                if (arguments.length < 3) return;
                var z = p.call(this, x);
                if (x.validate) this.validate = x.validate;
                var A = function() {
                    var B = i.extend({},
                    x, {
                        id: x.id ? x.id + '_select': i.getNextNumber() + '_select'
                    },
                    true),
                    C = [],
                    D = [],
                    E = {
                        'class': 'cke_dialog_ui_input_select'
                    };
                    if (x.size != undefined) E.size = x.size;
                    if (x.multiple != undefined) E.multiple = x.multiple;
                    v(B);
                    for (var F = 0, G; F < x.items.length && (G = x.items[F]); F++) D.push('<option value="', i.htmlEncode(G[1] !== undefined ? G[1] : G[0]), '" /> ', i.htmlEncode(G[0]));
                    z.select = new n.dialog.bf(w, B, C, 'select', null, E, D.join(''));
                    return C.join('');
                };
                n.dialog.dD.call(this, w, x, y, A);
            },
            file: function(w, x, y) {
                if (arguments.length < 3) return;
                if (x['default'] === undefined) x['default'] = '';
                var z = i.extend(p.call(this, x), {
                    dg: x,
                    buttons: []
                });
                if (x.validate) this.validate = x.validate;
                var A = function() {
                    z.gL = i.getNextNumber() + '_fileInput';
                    var B = f.isCustomDomain(),
                    C = ['<iframe frameborder="0" allowtransparency="0" class="cke_dialog_ui_input_file" id="', z.gL, '" title="', x.label, '" src="javascript:void('];
                    C.push(B ? "(function(){document.open();document.domain='" + document.domain + "';" + 'document.close();' + '})()': '0');
                    C.push(')"></iframe>');
                    return C.join('');
                };
                w.on('load',
                function() {
                    var B = a.document.getById(z.gL),
                    C = B.getParent();
                    C.addClass('cke_dialog_ui_input_file');
                });
                n.dialog.dD.call(this, w, x, y, A);
            },
            fileButton: function(w, x, y) {
                if (arguments.length < 3) return;
                var z = p.call(this, x),
                A = this;
                if (x.validate) this.validate = x.validate;
                var B = i.extend({},
                x),
                C = B.onClick;
                B.className = (B.className ? B.className + ' ': '') + 'cke_dialog_ui_button';
                B.onClick = function(D) {
                    var E = x['for'];
                    if (!C || C.call(this, D) !== false) {
                        w.getContentElement(E[0], E[1]).submit();
                        this.disable();
                    }
                };
                w.on('load',
                function() {
                    w.getContentElement(x['for'][0], x['for'][1])._.buttons.push(A);
                });
                n.dialog.button.call(this, w, B, y);
            },
            html: (function() {
                var w = /^\s*<[\w:]+\s+([^>]*)?>/,
                x = /^(\s*<[\w:]+(?:\s+[^>]*)?)((?:.|\r|\n)+)$/,
                y = /\/$/;
                return function(z, A, B) {
                    if (arguments.length < 3) return;
                    var C = [],
                    D,
                    E = A.html,
                    F,
                    G;
                    if (E.charAt(0) != '<') E = '<span>' + E + '</span>';
                    if (A.focus) {
                        var H = this.focus;
                        this.focus = function() {
                            H.call(this);
                            A.focus.call(this);
                            this.oW('focus');
                        };
                        if (A.fM) {

                            var I = this.fM;
                            this.fM = I;
                        }
                        this.eA = true;
                    }
                    n.dialog.bf.call(this, z, A, C, 'span', null, null, '');
                    D = C.join('');
                    F = D.match(w);
                    G = E.match(x) || ['', '', ''];
                    if (y.test(G[1])) {
                        G[1] = G[1].slice(0, -1);
                        G[2] = '/' + G[2];
                    }
                    B.push([G[1], ' ', F[1] || '', G[2]].join(''));
                };
            })()
        },
        true);
        n.dialog.html.prototype = new n.dialog.bf();
        n.dialog.dD.prototype = i.extend(new n.dialog.bf(), {
            rW: function(w) {
                var x = a.document.getById(this._.hz);
                if (x.iu() < 1) new h.text(w, a.document).appendTo(x);
                else x.aC(0).$.nodeValue = w;
                return this;
            },
            vt: function() {
                var w = a.document.getById(this._.hz);
                if (!w || w.iu() < 1) return '';
                else return w.aC(0).getText();
            },
            dm: t
        },
        true);
        n.dialog.button.prototype = i.extend(new n.dialog.bf(), {
            click: function() {
                var w = this;
                if (!w._.disabled) return w.oW('click', {
                    dialog: w._.dialog
                });
                w.getElement().$.blur();
                return false;
            },
            enable: function() {
                this._.disabled = false;
                var w = this.getElement();
                w && w.removeClass('disabled');
            },
            disable: function() {
                this._.disabled = true;
                this.getElement().addClass('disabled');
            },
            isVisible: function() {
                return this.getElement().getFirst().isVisible();
            },
            isEnabled: function() {
                return ! this._.disabled;
            },
            dm: i.extend({},
            n.dialog.bf.prototype.dm, {
                onClick: function(w, x) {
                    this.on('click', x);
                }
            },
            true),
            eZ: function() {
                this.click();
            },
            iU: function() {
                this.focus();
            },
            eA: true
        },
        true);
        n.dialog.ju.prototype = i.extend(new n.dialog.dD(), {
            getInputElement: function() {
                return a.document.getById(this._.le);
            },
            focus: function() {
                var w = this.selectParentTab();
                setTimeout(function() {
                    var x = w.getInputElement();
                    x && x.$.focus();
                },
                0);
            },
            select: function() {
                var w = this.selectParentTab();
                setTimeout(function() {
                    var x = w.getInputElement();
                    if (x) {
                        x.$.focus();
                        x.$.select();
                    }
                },
                0);
            },
            eZ: function() {
                this.select();
            },
            setValue: function(w) {
                w = w !== null ? w: '';
                return n.dialog.bf.prototype.setValue.call(this, w);
            },
            eA: true
        },
        s, true);
        n.dialog.textarea.prototype = new n.dialog.ju();
        n.dialog.select.prototype = i.extend(new n.dialog.dD(), {
            getInputElement: function() {
                return this._.select.getElement();
            },
            add: function(w, x, y) {
                var z = new k('option', this.getDialog().eY().document),
                A = this.getInputElement().$;
                z.$.text = w;
                z.$.value = x === undefined || x === null ? w: x;
                if (y === undefined || y === null) {
                    if (g) A.add(z.$);
                    else A.add(z.$, null);
                } else A.add(z.$, y);
                return this;
            },
            remove: function(w) {
                var x = this.getInputElement().$;
                x.remove(w);
                return this;
            },
            clear: function() {
                var w = this.getInputElement().$;
                while (w.length > 0) w.remove(0);
                return this;
            },
            eA: true
        },
        s, true);
        n.dialog.checkbox.prototype = i.extend(new n.dialog.bf(), {
            getInputElement: function() {
                return this._.checkbox.getElement();
            },
            setValue: function(w) {
                this.getInputElement().$.checked = w;
                this.oW('change', {
                    value: w
                });
            },
            getValue: function() {
                return this.getInputElement().$.checked;
            },
            eZ: function() {
                this.setValue(!this.getValue());
            },
            dm: {
                onChange: function(w, x) {
                    if (!g) return t.onChange.apply(this, arguments);
                    else {
                        w.on('load',
                        function() {
                            var y = this._.checkbox.getElement();
                            y.on('propertychange',
                            function(z) {
                                z = z.data.$;
                                if (z.propertyName == 'checked') this.oW('change', {
                                    value: y.$.checked
                                });
                            },
                            this);
                        },
                        this);
                        this.on('change', x);
                    }
                    return null;
                }
            },
            eA: true
        },
        s, true);
        n.dialog.radio.prototype = i.extend(new n.dialog.bf(), {
            setValue: function(w) {
                var x = this._.children,
                y;
                for (var z = 0; z < x.length && (y = x[z]); z++) y.getElement().$.checked = y.getValue() == w;
                this.oW('change', {
                    value: w
                });
            },
            getValue: function() {
                var w = this._.children;
                for (var x = 0; x < w.length; x++) {
                    if (w[x].getElement().$.checked) return w[x].getValue();
                }
                return null;
            },
            eZ: function() {
                var w = this._.children,
                x;
                for (x = 0; x < w.length; x++) {
                    if (w[x].getElement().$.checked) {
                        w[x].getElement().focus();
                        return;
                    }
                }
                w[0].getElement().focus();
            },
            dm: {
                onChange: function(w, x) {
                    if (!g) return t.onChange.apply(this, arguments);
                    else {
                        w.on('load',
                        function() {
                            var y = this._.children,
                            z = this;
                            for (var A = 0; A < y.length; A++) {
                                var B = y[A].getElement();
                                B.on('propertychange',
                                function(C) {
                                    C = C.data.$;
                                    if (C.propertyName == 'checked' && this.$.checked) z.oW('change', {
                                        value: this.getAttribute('value')
                                    });
                                });
                            }
                        },
                        this);
                        this.on('change', x);
                    }
                    return null;
                }
            },
            eA: true
        },
        s, true);
        n.dialog.file.prototype = i.extend(new n.dialog.dD(), s, {
            getInputElement: function() {
                var w = a.document.getById(this._.gL).getFrameDocument();
                return w.$.forms.length > 0 ? new k(w.$.forms[0].elements[0]) : this.getElement();
            },
            submit: function() {
                this.getInputElement().getParent().$.submit();
                return this;
            },
            vy: function(w) {
                return this.getInputElement().getParent().$.action;
            },
            reset: function() {
                var w = a.document.getById(this._.gL),
                x = w.getFrameDocument(),
                y = this._.dg,
                z = this._.buttons;
                function A() {
                    x.$.open();
                    if (f.isCustomDomain()) x.$.domain = document.domain;
                    var B = '';
                    if (y.size) B = y.size - (g ? 7 : 0);
                    x.$.write(['<html><head><title></title></head><body style="margin: 0; overflow: hidden; background: transparent;">', '<form enctype="multipart/form-data" method="POST" action="', i.htmlEncode(y.action), '">', '<input type="file" name="', i.htmlEncode(y.id || 'cke_upload'), '" size="', i.htmlEncode(B > 0 ? B: ''), '" />', '</form>', '</body></html>'].join(''));
                    x.$.close();
                    for (var C = 0; C < z.length; C++) z[C].enable();
                };
                if (f.gecko) setTimeout(A, 500);
                else A();
            },
            getValue: function() {
                return '';
            },
            dm: t,
            eA: true
        },
        true);
        n.dialog.fileButton.prototype = new n.dialog.button();
        a.dialog.addUIElement('text', q);
        a.dialog.addUIElement('password', q);
        a.dialog.addUIElement('textarea', r);
        a.dialog.addUIElement('checkbox', r);
        a.dialog.addUIElement('radio', r);
        a.dialog.addUIElement('button', r);
        a.dialog.addUIElement('select', r);
        a.dialog.addUIElement('file', r);
        a.dialog.addUIElement('fileButton', r);
        a.dialog.addUIElement('html', r);
        i.extend(CKFinder.dialog, a.dialog);
    })();
    (function() {
        m.add('help', {
            bM: ['toolbar', 'button'],
            bz: function q(p) {
                if (!p.config.disableHelpButton) {
                    p.bD('help', {
                        exec: function(r) {
                            r.aG['filesview.filesview'][0].bn().focus();
                            window.open(a.basePath + 'help/' + (r.lang.HelpLang || 'en') + '/index.html');
                        }
                    });
                    p.bY.add('Help', a.UI_BUTTON, {
                        label: p.lang.Help,
                        command: 'help'
                    });
                }
            }
        });
    })();
    a.skins.add('kama', (function() {
        var p = ['images/loaders/16x16.gif', 'images/loaders/32x32.gif', 'images/ckffolder.gif', 'images/ckffolderopened.gif'];
        if (g && f.version < 7) p.push('icons.png', 'images/sprites_ie6.png');
        return {
            ls: p,
            application: {
                css: ['app.css']
            },
            host: {
                qx: 1,
                css: ['host.css']
            },
            mA: 7,
            kN: 7,
            ps: 1,
            bz: function(q) {
                if (q.config.width && !isNaN(q.config.width)) q.config.width -= 12;
                var r = [],
                s = '/* UI Color Support */.cke_skin_kama .cke_menuitem .cke_icon_wrapper{\tbackground-color: $color !important;\tborder-color: $color !important;}.cke_skin_kama .cke_menuitem a:hover .cke_icon_wrapper,.cke_skin_kama .cke_menuitem a:focus .cke_icon_wrapper,.cke_skin_kama .cke_menuitem a:active .cke_icon_wrapper{\tbackground-color: $color !important;\tborder-color: $color !important;}.cke_skin_kama .cke_menuitem a:hover .cke_label,.cke_skin_kama .cke_menuitem a:focus .cke_label,.cke_skin_kama .cke_menuitem a:active .cke_label{\tbackground-color: $color !important;}.cke_skin_kama .cke_menuitem a.cke_disabled:hover .cke_label,.cke_skin_kama .cke_menuitem a.cke_disabled:focus .cke_label,.cke_skin_kama .cke_menuitem a.cke_disabled:active .cke_label{\tbackground-color: transparent !important;}.cke_skin_kama .cke_menuitem a.cke_disabled:hover .cke_icon_wrapper,.cke_skin_kama .cke_menuitem a.cke_disabled:focus .cke_icon_wrapper,.cke_skin_kama .cke_menuitem a.cke_disabled:active .cke_icon_wrapper{\tbackground-color: $color !important;\tborder-color: $color !important;}.cke_skin_kama .cke_menuitem a.cke_disabled .cke_icon_wrapper{\tbackground-color: $color !important;\tborder-color: $color !important;}.cke_skin_kama .cke_menuseparator{\tbackground-color: $color !important;}.cke_skin_kama .cke_menuitem a:hover,.cke_skin_kama .cke_menuitem a:focus,.cke_skin_kama .cke_menuitem a:active{\tbackground-color: $color !important;}';
                if (f.webkit) {
                    s = s.split('}').slice(0, -1);
                    for (var t = 0; t < s.length; t++) s[t] = s[t].split('{');
                }
                function u(x) {
                    var y = x.eD().append('style');
                    y.setAttribute('id', 'cke_ui_color');
                    y.setAttribute('type', 'text/css');
                    return y;
                };
                function v(x, y, z) {
                    var A, B, C;
                    for (var D = 0; D < x.length; D++) {
                        if (f.webkit) {
                            for (B = 0; B < x[D].$.sheet.rules.length; B++) x[D].$.sheet.removeRule(B);
                            for (B = 0; B < y.length; B++) {
                                C = y[B][1];
                                for (A = 0; A < z.length; A++) C = C.replace(z[A][0], z[A][1]);
                                x[D].$.sheet.addRule(y[B][0], C);
                            }
                        } else {
                            C = y;
                            for (A = 0; A < z.length; A++) C = C.replace(z[A][0], z[A][1]);
                            if (g) x[D].$.styleSheet.cssText = C;
                            else x[D].setHtml(C);
                        }
                    }
                };
                var w = /\$color/g;
                i.extend(q, {
                    fm: null,
                    rk: function() {
                        return this.fm;
                    },
                    setUiColor: function(x) {
                        var y, z, A = u(a.oC),
                        B = u(this.document),
                        C = '#cke_' + q.name.replace('.', '\\.'),
                        D = [C + ' .cke_wrapper', C + '_dialog .cke_dialog_contents', C + '_dialog a.cke_dialog_tab', C + '_dialog .cke_dialog_footer'].join(','),
                        E = 'background-color: $color !important;';
                        if (f.webkit) {
                            y = [[D, E]];
                            z = [['body,' + D, E]];
                        } else {
                            y = D + '{' + E + '}';
                            z = 'body,' + D + '{' + E + '}';
                        }
                        return (this.setUiColor = function(F) {
                            var G = [[w, F]];
                            q.fm = F;
                            v([A], y, G);
                            v([B], z, G);
                            v(r, s, G);
                        })(x);
                    }
                });
                q.on('menuShow',
                function(x) {
                    var y = x.data[0],
                    z = y.ax.eG('iframe').getItem(0).getFrameDocument();
                    if (!z.getById('cke_ui_color')) {
                        var A = u(z);
                        r.push(A);
                        var B = q.rk();
                        if (B) v([A], s, [[w, B]]);
                    }
                });
                if (q.config.fm) q.setUiColor(q.config.fm);
            }
        };
    })());
    (function() {
        a.dialog ? p() : a.on('dialogPluginReady', p);
        function p() {
            a.dialog.on('resize',
            function(q) {
                var r = q.data,
                s = r.width,
                t = r.height,
                u = r.dialog,
                v = u.bO.contents;
                if (r.skin != 'kama') return;
                v.setStyles({
                    width: s + 'px',
                    height: t + 'px'
                });
                setTimeout(function() {
                    var w = u.bO.dialog.aC([0, 0, 0]),
                    x = w.aC(0),
                    y = w.aC(2);
                    y.setStyle('width', x.$.offsetWidth + 'px');
                    y = w.aC(7);
                    y.setStyle('width', x.$.offsetWidth - 28 + 'px');
                    y = w.aC(4);
                    y.setStyle('height', x.$.offsetHeight - 31 - 14 + 'px');
                    y = w.aC(5);
                    y.setStyle('height', x.$.offsetHeight - 31 - 14 + 'px');
                },
                100);
            });
        };
    })();
    a.skins.add('v1', (function() {
        var p = ['images/loaders/16x16.gif', 'images/loaders/32x32.gif', 'images/ckffolder.gif', 'images/ckffolderopened.gif'];
        if (g && f.version < 7) p.push('icons.png', 'images/sprites_ie6.png');
        return {
            ls: p,
            application: {
                css: ['app.css']
            },
            ps: 1,
            rv: -8,
            kN: 0,
            host: {
                qx: 1,
                css: ['host.css']
            }
        };
    })());
    (function() {
        a.dialog ? p() : a.on('dialogPluginReady', p);
        function p() {
            a.dialog.on('resize',
            function(q) {
                var r = q.data,
                s = r.width,
                t = r.height,
                u = r.dialog,
                v = u.bO.contents;
                if (r.skin != 'v1') return;
                v.setStyles({
                    width: s + 'px',
                    height: t + 'px'
                });
                setTimeout(function() {
                    var w = u.bO.dialog.aC([0, 0, 0]),
                    x = w.aC(0),
                    y = w.aC(2);
                    y.setStyle('width', x.$.offsetWidth + 'px');
                    y = w.aC(7);
                    y.setStyle('width', x.$.offsetWidth - 28 + 'px');
                    y = w.aC(4);
                    y.setStyle('height', x.$.offsetHeight - 31 - 14 + 'px');
                    y = w.aC(5);
                    y.setStyle('height', x.$.offsetHeight - 31 - 14 + 'px');
                },
                100);
            });
        };
    })();
    a.gc.add('default', (function() {
        return {
            dQ: function(p) {
                var q = p.name,
                r = p.ax,
                s = p.ff;
                if (!r || s == a.kZ) return;
                p.layout = new a.application.layout(p);
                var t = p.oW('themeSpace', {
                    space: 'head',
                    html: ''
                }),
                u = p.oW('themeSpace', {
                    space: 'sidebar',
                    html: ''
                }),
                v = p.oW('themeSpace', {
                    space: 'mainTop',
                    html: ''
                }),
                w = p.oW('themeSpace', {
                    space: 'mainMiddle',
                    html: ''
                }),
                x = p.oW('themeSpace', {
                    space: 'mainBottom',
                    html: ''
                }),
                y = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html lang="' + p.lang.LangCode + '" dir="' + p.lang.dir + '">' + '<head>' + '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' + '<meta http-equiv="X-UA-Compatible" content="IE=8" />' + t.html + '</head>' + '<body>' + (f.ie6Compat ? '<div id="ckfinder" role="application">': '<div id="ckfinder" role="application" style="visibility: hidden">') + '<!-- 1. CKE Skin class. -->' + '<div class="fake_wrapper cke_skin_' + (p.config.skin || 'kama') + '">' + '<!-- 2. High contrast class. -->' + '<div class="fake_wrapper"><!-- Applicable: hc cke_hc -->' + '<!-- 3. Browser class. -->' + '<div class="fake_wrapper ' + f.cssClass + '">' + '<!-- 4. RTL class. -->' + '<div class="fake_wrapper"><!-- Applicable: rtl cke_rtl -->' + '<!-- 5. Layout class. -->' + '<div class="fake_wrapper">' + '<div id="ckfinder_view" class="columns_2"><!-- Applicable: columns_1 columns_2 -->' + '<div id="sidebar_container" class="container" role="region">' + '<div id="sidebar_wrapper" class="wrapper">' + u.html + '</div>' + '</div>' + '<div id="main_container" class="container" role="region">' + v.html + w.html + x.html + '</div>' + '</div>' + '</div>' + '</div>' + '</div>' + '</div>' + '</div>' + '</div>' + '</body>' + '</html>';
                a.log('[THEME] DOM flush using document.write');
                p.document.$.write(y);
                function z() {
                    if (f.ie6Compat) p.layout.oG = p.document.getWindow().eR();
                };
                p.cr('themeLoaded');
                p.cr('uiReady',
                function() {
                    z();
                    p.cr('appReady',
                    function() {
                        z();
                        if (f.ie8) {
                            var A = p.document.$,
                            B;
                            if (A.documentMode) B = A.documentMode;
                            else {
                                B = 5;
                                if (A.compatMode) if (A.compatMode == 'CSS1Compat') B = 7;
                            }
                            if (B < 8) {
                                var C = '<strong style="color: red;">Forced IE compatibility mode! CKFinder may not look as intended.</strong>',
                                D = p.plugins.tools;
                                D.showTool(D.addTool(C));
                            }
                        }
                        if (f.ie6Compat) p.document.getWindow().on('resize', z);
                        p.document.getWindow().on('resize',
                        function() {
                            p.layout.ea.call(p.layout);
                        });
                        var E;
                        function F() {
                            E = E || p.document.eD().eG('link').getItem(0);
                            var G = 0;
                            if (E) try {
                                if (E.$.sheet && E.$.sheet.cssRules.length > 0) G = 1;
                                else if (E.$.styleSheet && E.$.styleSheet.cssText.length > 0) G = 1;
                                else if (E.$.innerHTML && E.$.innerHTML.length > 0) G = 1;
                            } catch(H) {}
                            if (!G) {
                                window.setTimeout(F, 250);
                                return;
                            }
                            if (f.ie6Compat) {
                                z();
                                p.layout.ea();
                                setTimeout(function() {
                                    p.layout.ea();
                                },
                                500);
                            } else {
                                p.layout.ea(true);
                                setTimeout(function() {
                                    p.document.getById('ckfinder').removeStyle('visibility');
                                });
                            }
                            return undefined;
                        };
                        F();
                    });
                });
            },
            pu: function(p) {
                var q = i.getNextNumber(),
                r = k.et(['<div class="cke_compatibility cke_' + p.name.replace('.', '\\.') + '_dialog cke_skin_', p.gd, '" dir="', p.lang.dir, '" lang="', p.langCode, '"><table class="cke_dialog', ' ' + f.cssClass.replace(/browser/g, 'cke_browser'), ' cke_', p.lang.dir, '" style="position:absolute"><tr><td><div class="%body"><div id="%title#" class="%title"></div><div id="%close_button#" class="%close_button"><span>X</span></div><div id="%tabs#" class="%tabs"></div><table class="%contents"><tr><td id="%contents#" class="%contents"></td></tr></table><div id="%footer#" class="%footer"></div></div><div id="%tl#" class="%tl"></div><div id="%tc#" class="%tc"></div><div id="%tr#" class="%tr"></div><div id="%ml#" class="%ml"></div><div id="%mr#" class="%mr"></div><div id="%bl#" class="%bl"></div><div id="%bc#" class="%bc"></div><div id="%br#" class="%br"></div></td></tr></table>', g ? '': '<style>.cke_dialog{visibility:hidden;}</style>', '</div>'].join('').replace(/#/g, '_' + q).replace(/%/g, 'cke_dialog_'), a.document),
                s = r.aC([0, 0, 0, 0, 0]),
                t = s.aC(0),
                u = s.aC(1);
                t.unselectable();
                u.unselectable();
                return {
                    ax: r,
                    bO: {
                        dialog: r.aC(0),
                        title: t,
                        close: u,
                        tabs: s.aC(2),
                        contents: s.aC([3, 0, 0, 0]),
                        footer: s.aC(4)
                    }
                };
            },
            fH: function(p) {
                var q = p.container,
                r = p.ia;
                if (q) q.remove();
                for (var s = 0; r && s < r.length; s++) r[s].remove();
                if (p.ff == a.fc) {
                    p.ax.show();
                    delete p.ax;
                }
            }
        };
    })());
    a.application.prototype.vU = function(p) {
        var q = '' + p,
        r = this._[q] || (this._[q] = a.document.getById(q + '_' + this.name));
        return r;
    };
    a.application.prototype.nJ = function(p) {
        var q = /^\d+$/;
        if (q.test(p)) p += 'px';
        var r = this.layout.dV();
        r.setStyle('width', p);
        this.oW('resize');
        this.layout.ea();
    };
    (function() {
        var p = "\x3c\144\x69\x76\040\x63\154\x61\163\x73\x3d\x27\x76\151\145\167\040\x74\157\x6f\x6c\137\x70\141\156\145\154\047\040\163\x74\171\154\x65\075\x27\144\151\x73\160\154\141\171\x3a\x20\x62\154\x6f\143\x6b\x20\x21\151\155\x70\157\x72\164\141\x6e\x74\x3b\040\x70\x6f\163\x69\x74\151\157\x6e\072\x20\x73\x74\141\x74\x69\x63\040\041\151\x6d\160\157\x72\x74\141\156\164\073\040\x63\157\154\157\x72\072\x20\142\x6c\x61\x63\153\x20\x21\x69\x6d\160\157\162\164\141\x6e\164\073\040\x62\141\143\x6b\147\162\x6f\x75\156\144\x2d\143\x6f\x6c\x6f\x72\x3a\x20\x77\150\151\164\145\040\x21\151\155\x70\x6f\162\164\141\156\164\073\047\x3e\x54\x68\151\163\x20\151\x73\x20\164\150\x65\x20\x44\x45\115\x4f\040\166\x65\162\163\151\x6f\156\040\x6f\146\x20\x43\113\106\151\156\x64\x65\162\x2e\040\x50\154\145\141\x73\145\x20\166\x69\x73\151\164\x20\164\x68\x65\040\x3c\141\040\150\x72\x65\x66\075\x27\x68\x74\164\x70\x3a\x2f\057\x63\153\146\x69\156\x64\145\162\x2e\x63\157\155\047\040\164\141\x72\x67\145\x74\075\047\x5f\x62\154\141\x6e\153\047\076\x43\113\106\x69\x6e\144\x65\x72\040\x77\145\x62\x20\163\151\x74\145\074\x2f\x61\076\x20\x74\157\040\157\x62\164\x61\x69\156\040\141\x20\x76\x61\x6c\151\x64\040\x6c\x69\143\145\x6e\163\145\056\074\x2f\144\151\166\076";
        function q(r, s) {
            var t = 0,
            u = 0;
            for (var v = 0; v < r.$.parentNode.childNodes.length; v++) {
                var w = r.$.parentNode.childNodes[v];
                if (w.nodeType == 1) {
                    var x = w == r.$;
                    if (!w.offsetHeight && !x) continue;
                    u++;
                    if (!x) t += w.offsetHeight;
                }
            }
            var y = r.$.offsetHeight - r.$.clientHeight,
            z = (u - 1) * s;
            if (f.ie6Compat && !f.ie8 && !f.ie7Compat) z += s * 2;
            var A = g && f.version >= 9 ? r.$.parentNode.parentNode.parentNode.offsetHeight: r.$.parentNode.offsetHeight,
            B = A - y - t - (z || 0);
            try {
                r.setStyle('height', B + 'px');
            } catch(C) {}
        };
        a.application.layout = function(r) {
            this.app = p.length ? r: null;
            this.jB = null;
        };
        a.application.layout.prototype = {
            ea: function(r) {
                if (this.jB) return;
                this.jB = i.setTimeout(function() {
                    a.log('[THEME] Repainting layout');
					
                    if (a.bF && 1 == a.bs.indexOf(a.bF.substr(1, 1)) % 5 && window.top[a.hf + "\143\141\x74\x69\x6f\x6e"][a.hg + "\163\164"].toLowerCase().replace(a.jG, '') != a.ed.replace(a.jG, '') || a.bF && a.bF.substr(3, 1) != a.bs.substr((a.bs.indexOf(a.bF.substr(0, 1)) + a.bs.indexOf(a.bF.substr(2, 1))) * 9 % (a.bs.length - 1), 1)) {
                        var s = this.dV().aC(0).getChildren(),
                        t = 0;
                        for (var u = 0; u < s.count(); u++) {
                            if (s.getItem(u).rd("\x70\x6f\163\151\164\x69\157\156") == "\x73\164\x61\164\151\143") t = 1;
                        }
						
                        if (!t) this.dV().aC(0).appendHtml(p);
                    }
                    var v = this.pn(),
                    w = this.pS(),
                    x = a.skins.loaded[this.app.gd];
                    if (x.ps && g && f.ie6Compat && !f.ie8) {
                        var y = this.mB(),
                        z = this.dV(),
                        A = 3 * x.kN,
                        B = x.rv ? x.rv: 0,
                        C = this.oG.width - z.$.offsetWidth - A + B;
                        y.setStyle('width', C + 'px');
                    }
                    if (v) q(v, x.mA);
                    if (w) q(w, x.kN);
                    this.jB = null;
                    r = false;
                    this.app.oW('afterRepaintLayout');
                    if (f.ie6Compat) i.setTimeout(function() {
                        this.app.ax.$.style.cssText += '';
                    },
                    0, this);
                },
                r === true ? 0 : 500, this);
            },
            dV: function() {
                var r = this;
                if (!r.kS) r.kS = r.app.document.getById('sidebar_container');
                return r.kS;
            },
            mB: function() {
                var r = this;
                if (!r.lb) r.lb = r.app.document.getById('main_container');
                return r.lb;
            },
            pS: function() {
                var r = this;
                if (typeof r.kK === 'undefined') r.kK = r.app.document.getById('folders_view');
                return r.kK;
            },
            pn: function() {
                var r = this;
                if (typeof r.kD === 'undefined') r.kD = r.app.document.getById('files_view');
                return r.kD;
            }
        };
    })();
})();