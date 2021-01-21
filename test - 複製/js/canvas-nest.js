import CanvasNest from 'canvas-nest.js';

const config = {
  color: '255,0,0',
  count: 88,
};

// 在 element 地方使用 config 渲染效果
const cn = new CanvasNest(element, config);

// destroy
cn.destroy();