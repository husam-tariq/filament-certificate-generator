const deleteIcon = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='595.275px' height='595.275px' viewBox='200 215 230 470' xml:space='preserve'%3E%3Ccircle style='fill:%23F44336;' cx='299.76' cy='439.067' r='218.516'/%3E%3Cg%3E%3Crect x='267.162' y='307.978' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -222.6202 340.6915)' style='fill:white;' width='65.545' height='262.18'/%3E%3Crect x='266.988' y='308.153' transform='matrix(0.7071 0.7071 -0.7071 0.7071 398.3889 -83.3116)' style='fill:white;' width='65.544' height='262.179'/%3E%3C/g%3E%3C/svg%3E";
var drawer=null;
export default function certificateEditor({
                                              state,
                                              canvasData,
                                              options
                                          }) {
    return {
        state,
        canvas:null,
        certData:[],
        canvasData,
        options,
        init() {
            state = state.initialValue;

            const canvas = new fabric.Canvas(this.$refs.containerRef);
            drawer = canvas;
            fabric.Image.fromURL(canvasData.imageURL, function (img) {
                canvas.setHeight(canvasData.canvasHeight);
                canvas.setWidth(canvasData.canvasWidth);
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
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
                canvas.getActiveObject().fill = this.value;
                canvas.getActiveObject().dirty = true;
                state[canvas.getActiveObject().id]['color'] = this.value;
                canvas.renderAll();
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
                delete state[target['id']];
                canvas.remove(target);
                canvas.requestRenderAll();
            }

            let items = JSON.parse(JSON.stringify(state));
            console.log(items);
            items = Object.keys(items).map((key) => [key, items[key]]);

            items.forEach(function (e) {
                if (typeof e[1].startY !== 'undefined') {
                    let text = new fabric.IText(options[e[0]], {
                        id: e[0],
                        left: ((e[1].startY ?? canvasData.coefficient) / canvasData.coefficient),
                        top: ((e[1].startX ?? canvasData.coefficient) / canvasData.coefficient),
                        scaleX: e[1]?.scaleX ?? 1,
                        scaleY: e[1]?.scaleY ?? 1,
                        type: 'i-text',
                        objectCaching: false,
                        editable: false,
                        //fontFamily: 'helvetica neue',
                        fill: e[1]?.color ?? '#000',
                        stroke: '#fff',
                        strokeWidth: .1,
                        fontSize: 45,
                    });
                    canvas.add(text);
                    text.on('selected', function () {
                        ITextColor.value = text.fill;
                    });
                }
            })
            canvas.on('object:moving', function (e) {
                console.log(this.state);
                this.state[e.target['id']]['scale'] = canvasData.coefficient;
                this.state[e.target['id']]['startX'] = (canvasData.coefficient * e.target['top']);
                this.state[e.target['id']]['startY'] = (canvasData.coefficient * e.target['left']);
                this.state[e.target['id']]['width'] = ((canvasData.coefficient * e.target['width']) * e.target['scaleX']);
                this.state[e.target['id']]['height'] = ((canvasData.coefficient * e.target['height']) * e.target['scaleY']);
                this.state[e.target['id']]['scaleY'] = e.target['scaleY'];
                this.state[e.target['id']]['scaleX'] = e.target['scaleX'];
                console.log(this.state);
            });
            canvas.on('selection:created', function (e) {
                if (e.selected[0].type === 'i-text') {
                    document.getElementById('textControls').hidden = false;
                    ITextColor.value = e.selected[0].fill;
                }
            });
            canvas.on('before:selection:cleared', function (e) {
                if (e.target.type === 'i-text') {
                    document.getElementById('textControls').hidden = true;
                }
            });
            canvas.on('object:modified', function (e) {
                this.state[e.target['id']]['scale'] = canvasData.coefficient;
                this.state[e.target['id']]['startX'] = (canvasData.coefficient * e.target['top']);
                this.state[e.target['id']]['startY'] = (canvasData.coefficient * e.target['left']);
                this.state[e.target['id']]['width'] = ((canvasData.coefficient * e.target['width']) * e.target['scaleX']);
                this.state[e.target['id']]['height'] = ((canvasData.coefficient * e.target['height']) * e.target['scaleY']);
                this.state[e.target['id']]['scaleY'] = e.target['scaleY'];
                this.state[e.target['id']]['scaleX'] = e.target['scaleX'];
                console.log(this.state);
            });


        },
        updateData: function (){

        },
        addText:  function (id, text) {
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
                    objectCaching: false,
                    fontFamily: 'helvetica neue',
                    fill: '#000',
                    stroke: '#fff',
                    strokeWidth: .1,
                    fontSize: 45,
                });
                drawer.add(e);
                let tmp = this.state;
                this.state=[];
                console.log(this.state);
                console.log(tmp);
                tmp[id] = {
                    'startY': (this.canvasData.coefficient * e['left']),
                    'startX': (this.canvasData.coefficient * e['top']),
                    'scale': (this.canvasData.coefficient),
                    'width': ((this.canvasData.coefficient * e['width']) * e['scaleX']),
                    'height': ((this.canvasData.coefficient * e['height']) * e['scaleY']),
                    'scaleY': e['scaleY'],
                    'scaleX': e['scaleX'],
                };
                this.state=tmp;
                console.log(this.state);
            }

        }

    }
}
