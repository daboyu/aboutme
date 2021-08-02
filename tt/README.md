# 如何寫新網誌文章

1. 於 `docs/blog/` 新增檔案
2. 撰寫 Front Matter
    ```yaml
    date: "2019-12-24 00:00"
    description: 當前頁面的描述
    fluid: true # 設定當前頁面的 layout 為 .container-fluid 而不是 .container
    lang: zh-TW
    metaTitle: 重寫默認的 meta title
    permalink: 参考 https://v1.vuepress.vuejs.org/zh/guide/frontmatter.html#permalink
    tags: ['文章的標籤']
    title: 當前頁面的標題
    meta:
    - name: keywords
      content: LINE,Flex Message,Simulator
    - property: og:url
      content: https://taichunmin.idv.tw/blog/2020-04-06-line-devbot.html
    - property: og:title
      content: 輔助開發 LINE Flex 訊息的工具
    - property: og:description
      content: 在官方模擬器看到的結果，不一定跟手機上面的相同，可以使用這個小工具來幫助你喔！
    - property: og:image
      content: https://i.imgur.com/3vUdhWw.png (最佳解析度 600x314@2)
    - property: og:image:width
      content: 600
    - property: og:image:height
      content: 314
    - property: og:type
      content: article
    ```
