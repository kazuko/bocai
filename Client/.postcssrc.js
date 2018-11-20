// https://github.com/michael-ciniawsky/postcss-load-config

module.exports = {
  plugins: {
    "postcss-import": {},
    "postcss-url": {},
    // to edit target browsers: use "browserslist" field in package.json
    autoprefixer: {},
    "postcss-px-to-viewport": {
      //http://web.jobbole.com/94049/
      viewportWidth: 375, //默认视图宽度
      unitPrecision: 3, //保留的小数位
      viewportUnit: "vw", //要转换成
      selectorBlackList: [".ignore"], //过滤类名
      propertyBlacklist: ["border"], //过滤属性
      minPixelValue: 1, //小于等于1px不转换
      mediaQuery: false //媒体查询中的属性是否需要转换
    }
  }
};
