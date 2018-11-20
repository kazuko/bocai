// 1 ，4 ，10 输出成 01 ，04，10
const filter_toTwo = value => {
  return value < 10 ? "0" + value : value;
};

// 4舍5入到指定位数
const filter_toFixed = (value, num) => {
  let ifNumber = !isNaN(value);
  return ifNumber ? Number(value).toFixed(num) : value;
};

export { filter_toTwo, filter_toFixed };
