"use strict";

class Point {
    constructor(x, y, color, angle, step) {
        this.x = x;
        this.y = y;
        this.color = color;
        this.step = step;
        this.angle = angle;
        this.radius = 75;
        this.setDirection();
    }

    setDirection() {
        this.dx = Math.sin(this.angle * Math.PI / 180) * this.step;
        this.dy = Math.cos(this.angle * Math.PI / 180) * this.step;
    }

    move(field, vector={x:0, y:0}) {
        if (this.x <= 0 || this.x >= field.pWidth) this.dx = - this.dx;
        else if (this.y <= 0 || this.y >= field.pHeight) this.dy = this.dy;

        this.x += this.dx + vector.x / 100;
        this.y += this.dy + vector.y / 100;
        this.x = parseFloat(this.x.toFixed(1));
        this.y = parseFloat(this.y.toFixed(1));
    }

    nearby(point) {
        return Math.pow(point.x - this.x, 2) + Math.pow(point.y - this.y, 2) <= Math.pow(this.radius, 2)
    }

    getConnect(points, field) {
        points.forEach(point => {
            if (this.nearby(point)) field.drawConnect(point.x, point.y, this.x, this.y)
    })
    }
}

class Field {
    constructor(width, height, pointSize, canvas) {
        this.pointSize = pointSize;
        this.width = width * pointSize;
        this.pWidth = width;
        this.height = height * pointSize;
        this.pHeight = height;
        this.middle = this.pointSize / 2;
        this.dc = canvas.getContext("2d");
        canvas.width = this.width;
        canvas.height = this.height;
    }

    clear() {
        this.dc.fillStyle = "#113366";
        this.dc.clearRect(0, 0, this.width, this.height);
        this.dc.fillRect(0, 0, this.width, this.height);
    }

    drawPoint(point) {
        this.dc.fillStyle = point.color;
        this.dc.beginPath();
        this.dc.arc(
            point.x * this.pointSize + this.middle,
            point.y * this.pointSize + this.middle,
            this.pointSize, -Math.PI / 2, 3 * Math.PI / 2);
        this.dc.fill();
    }

    drawConnect(x1, y1, x2, y2) {
        this.dc.lineWidth = 1;
        this.dc.strokeStyle = "rgba(255,255,255,0.1)";
        this.dc.beginPath();
        this.dc.moveTo(x1 * this.pointSize + this.middle, y1 * this.pointSize + this.middle);
        this.dc.lineTo(x2 * this.pointSize + this.middle, y2 * this.pointSize + this.middle);
        this.dc.stroke();
    }
}

let starfield = {
    init: function () {
        this.view.init(this);
    }
};

starfield.view = {
    init: function (parent) {
        this.points = [];
        this.parent = parent;
        this.canvas = document.querySelector("canvas");
        this.ps = 1.5;
        this.createField(window.innerWidth / this.ps, window.innerHeight / this.ps, this.ps);
        this.generatePoints(250);
        this.mover();
    },
    createField: function (width, height, pointSize) {
        this.field = new Field(width, height, pointSize, this.canvas);
        this.field.clear();
    },
    generatePoints: function (amount) {
        for (let i = 0; i <= amount; i++) {
            this.points.push(this._createPoint());
        }
    },
    _getRandom: function (end, start = 0) {
        return Math.floor(Math.random() * (end - start) + 1)
    },
    _createPoint: function () {
        return new Point(
            this._getRandom(this.field.pWidth),
            this._getRandom(this.field.pHeight),
            "rgba(255,255,255,0.9)",
            this._getRandom(360),
            this._getRandom(5) / 10);
    },
    mover: function () {
        let timer = setInterval(() => {
            this.field.clear();
        this.points.forEach((point) => point.move(this.field, this.mouse ? this.vector : {x: 0, y: 0}));
        this.points.forEach((point) => this.field.drawPoint(point));
        this.points.forEach((point, currentIndex) => {
            point.getConnect(
            this.points.filter((point, index) => index !== currentIndex),
            this.field)
    })
    }, 50)
    }
};
document.onmousewheel=document.onwheel=function(){
    return false;
};
document.addEventListener("MozMousePixelScroll",function(){return false},false);
document.onkeydown=function(e) {
    if (e.keyCode>=33&&e.keyCode<=40) return false;
};
document.addEventListener("DOMContentLoaded", () => {
    starfield.init();
});