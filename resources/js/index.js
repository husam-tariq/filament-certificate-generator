const deleteIcon = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='595.275px' height='595.275px' viewBox='200 215 230 470' xml:space='preserve'%3E%3Ccircle style='fill:%23F44336;' cx='299.76' cy='439.067' r='218.516'/%3E%3Cg%3E%3Crect x='267.162' y='307.978' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -222.6202 340.6915)' style='fill:white;' width='65.545' height='262.18'/%3E%3Crect x='266.988' y='308.153' transform='matrix(0.7071 0.7071 -0.7071 0.7071 398.3889 -83.3116)' style='fill:white;' width='65.544' height='262.179'/%3E%3C/g%3E%3C/svg%3E";
var drawer = null;
export default function certificateEditor({
                                              state,
                                              canvasData,
                                              options
                                          }) {
    return {
        state,
        canvas: null,
        certData: [],
        canvasData,
        options,
        init() {
            this.updateData()
            const canvas = new fabric.Canvas(this.$refs.containerRef);
            this.canvas = canvas
            drawer = canvas
            var self = this;

            fabric.Image.fromURL(canvasData.imageURL, function (img) {
                self.canvas.setHeight(canvasData.canvasHeight);
                self.canvas.setWidth(canvasData.canvasWidth);
                self.canvas.setBackgroundImage(img, self.canvas.renderAll.bind(self.canvas), {
                    scaleX: self.canvas.width / img.width,
                    scaleY: self.canvas.height / img.height
                });
            });
            let ITextColor = document.getElementById('ITextColor');
            let deleteImg = document.createElement('img');
            deleteImg.src = deleteIcon;

            function renderIcon(icon) {
                return function renderIcon(ctx, left, top, styleOverride, fabricObject) {
                    var size = this.cornerSize;
                    ctx.save();
                    ctx.translate(left, top);
                    ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle));
                    ctx.drawImage(icon, -size / 2, -size / 2, size, size);
                    ctx.restore();
                }
            }

            ITextColor.addEventListener('input', function () {

                self.canvas.getActiveObject().dirty = true;
                if(self.canvas.getActiveObject().type==="i-text"){
                    self.canvas.getActiveObject().fill = this.value;
                }else if(self.canvas.getActiveObject().type==="image"){
                    self.canvas.getActiveObject().filters[0].color= this.value;
                    self.canvas.getActiveObject().applyFilters();
                }
                self.state[self.canvas.getActiveObject().id]['color'] = this.value;
                self.canvas.renderAll();
            })


            fabric.Object.prototype.controls.deleteControl = new fabric.Control({
                x: 0.5,
                y: -0.5,
                offsetY: -16,
                offsetX: 16,
                cursorStyle: 'pointer',
                mouseUpHandler: deleteObject,
                render: renderIcon(deleteImg),
                cornerSize: 24
            });


            function deleteObject(eventData, transform) {
                var target = transform.target;
                var canvas = target.canvas;
                delete self.state[target['id']];
                canvas.remove(target);
                canvas.requestRenderAll();
            }


            if (this.certData.length <= 0) {
                let [key, value] = Object.entries(this.options)[0]
                this.addText(key, value)
            } else {
                this.certData.forEach(function (e) {
                    if (typeof e.value.startY !== 'undefined') {
                       if(e.value.type==="text"){
                           let text = new fabric.IText(options[e.key], {
                               id: e.key,
                               left: ((e.value.startY ?? canvasData.coefficient) / canvasData.coefficient),
                               top: ((e.value.startX ?? canvasData.coefficient) / canvasData.coefficient),
                               scaleX: e.value?.scaleX ?? 1,
                               scaleY: e.value?.scaleY ?? 1,
                               type: 'i-text',
                               objectCaching: false,
                               editable: false,
                               //fontFamily: 'helvetica neue',
                               fill: e.value?.color ?? '#000',
                               stroke: '#fff',
                               strokeWidth: .1,
                               fontSize: 45,
                           });
                           canvas.add(text);
                           text.on('selected', function () {
                               ITextColor.value = text.fill;
                           });
                       }else if(e.value.type==="qr"){
                           fabric.Image.fromURL("/certificate-generator/qr/n/n/n", function(img) {
                               var oImg = img.set({
                                   id: "qr",
                                   left: ((e.value.startY ?? canvasData.coefficient) / canvasData.coefficient),
                                   top: ((e.value.startX ?? canvasData.coefficient) / canvasData.coefficient),
                                   scaleX: e.value?.scaleX ?? 1,
                                   scaleY: e.value?.scaleY ?? 1,
                                   angle: 0,
                                   border: '#000',
                                   stroke: '#fff',
                                   strokeWidth: .1,
                               });
                               oImg.setControlVisible('mtr',false)
                               oImg.setControlVisible('mb',false)
                               oImg.setControlVisible('mt',false)
                               oImg.setControlVisible('ml',false)
                               oImg.setControlVisible('mr',false)
                               let f = fabric.Image.filters
                               oImg.filters[0]=new f.BlendColor({
                                   color: e.value?.color ??"#000000",
                                   mode: "add",
                                   alpha: 1
                               })
                               oImg.applyFilters()
                               canvas.add(oImg).renderAll();
                           });
                       }
                    }
                })
            }


            this.canvas.on('object:moving', function (e) {
                self.state[e.target['id']]['scale'] = canvasData.coefficient;
                self.state[e.target['id']]['startX'] = (canvasData.coefficient * e.target['top']);
                self.state[e.target['id']]['startY'] = (canvasData.coefficient * e.target['left']);
                self.state[e.target['id']]['width'] = ((canvasData.coefficient * e.target['width']) * e.target['scaleX']);
                self.state[e.target['id']]['height'] = ((canvasData.coefficient * e.target['height']) * e.target['scaleY']);
                self.state[e.target['id']]['scaleY'] = e.target['scaleY'];
                self.state[e.target['id']]['scaleX'] = e.target['scaleX'];
            });
            this.canvas.on('selection:created', function (e) {
                if (e.selected[0].type === 'i-text' ) {
                    document.getElementById('textControls').hidden = false;
                    ITextColor.value = e.selected[0].fill;
                }else if(e.selected[0].type === 'image'){
                    document.getElementById('textControls').hidden = false;
                    ITextColor.value = e.selected[0].filters[0].color;
                }
            });
            this.canvas.on('before:selection:cleared', function (e) {
                if (e.target.type === 'i-text' || e.target.type === 'image') {
                    document.getElementById('textControls').hidden = true;
                }
            });
            this.canvas.on('object:modified', function (e) {
                self.state[e.target['id']]['scale'] = canvasData.coefficient;
                self.state[e.target['id']]['startX'] = (canvasData.coefficient * e.target['top']);
                self.state[e.target['id']]['startY'] = (canvasData.coefficient * e.target['left']);
                self.state[e.target['id']]['width'] = ((canvasData.coefficient * e.target['width']) * e.target['scaleX']);
                self.state[e.target['id']]['height'] = ((canvasData.coefficient * e.target['height']) * e.target['scaleY']);
                self.state[e.target['id']]['scaleY'] = e.target['scaleY'];
                self.state[e.target['id']]['scaleX'] = e.target['scaleX'];
            });


        },

        updateData: function () {
            let certData = []

            for (let [key, value] of Object.entries(this.state ?? {})) {
                certData.push({
                    key,
                    value,
                })
            }
            this.certData = certData
        },
        addText: function (id, text) {
            let state = this.state ?? {}
            var isExist = false
            drawer._objects.forEach(function (e) {
                if (e['id'] === id)
                    isExist = true;
            })
            if (isExist === false) {
                let e = new fabric.IText(text, {
                    id: id,
                    left: (drawer.width / 2),
                    top: (drawer.height / 2),
                    type: 'i-text',
                    objectCaching: false,
                    editable: false,
                    fill: '#000',
                    stroke: '#fff',
                    strokeWidth: .1,
                    fontSize: 45,
                });
                drawer.add(e)
                state[id] = {
                    'startY': (this.canvasData.coefficient * e['left']),
                    'startX': (this.canvasData.coefficient * e['top']),
                    'scale': (this.canvasData.coefficient),
                    'width': ((this.canvasData.coefficient * e['width']) * e['scaleX']),
                    'height': ((this.canvasData.coefficient * e['height']) * e['scaleY']),
                    'type': "text",
                    'scaleY': e['scaleY'],
                    'scaleX': e['scaleX'],
                };
                this.state = state
            }

        },addQr:  function ()  {
            let state = this.state ?? {}
            var isExist = false
            drawer._objects.forEach(function (e) {
                if (e['id'] === "qr")
                    isExist = true;
            })
            if (isExist === false) {
                var self = this;
                fabric.Image.fromURL("/certificate-generator/qr/n/n/n", function(img) {
                   var oImg = img.set({
                        id: "qr",
                        left: 0,
                        top: 0,
                        angle: 0,
                        border: '#000',
                        stroke: '#fff',
                        strokeWidth: .1,
                    }).scale(0.2);
                    oImg.setControlVisible('mtr',false)
                    oImg.setControlVisible('mb',false)
                    oImg.setControlVisible('mt',false)
                    oImg.setControlVisible('ml',false)
                    oImg.setControlVisible('mr',false)
                    let f = fabric.Image.filters
                    oImg.filters[0]=new f.BlendColor({
                        color: "#000000",
                        mode: "add",
                        alpha: 1
                    })
                    oImg.applyFilters()
                    drawer.add(oImg).renderAll();
                    state["qr"] = {
                        'startY': (self.canvasData.coefficient * oImg['left']),
                        'startX': (self.canvasData.coefficient * oImg['top']),
                        'scale': (self.canvasData.coefficient),
                        'width': ((self.canvasData.coefficient * oImg['width']) * oImg['scaleX']),
                        'height': ((self.canvasData.coefficient * oImg['height']) * oImg['scaleY']),
                        'type': "qr",
                        'scaleY': oImg['scaleY'],
                        'scaleX': oImg['scaleX'],
                    };
                    self.state = state
                });



            }

        }

    }
}
