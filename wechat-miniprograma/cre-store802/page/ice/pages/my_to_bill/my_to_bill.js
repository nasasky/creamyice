var config = require('../../../../config');

Page({
  data: {
    countries: ["2017", "2018", "2019"],
    countryIndex: 0,
  },
  bindCountryChange: function (e) {
    console.log('picker country 发生选择改变，携带值为', e.detail.value);

    this.setData({
      countryIndex: e.detail.value
    })
  },
});