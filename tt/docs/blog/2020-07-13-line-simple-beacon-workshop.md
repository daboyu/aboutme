---
date: "2020-07-13T00:00:00+08"
title: LINE Simple Beacon for ESP32 工作坊
description: 用 ESP32-DevKitC 來做一個自己的 LINE Simple Beacon 吧！
image: https://i.imgur.com/39XtRgF.png
tags:
  - LINE
  - Beacon
  - workshop
meta:
  - property: og:image:width
    content: 1200
  - property: og:image:height
    content: 630
---

# LINE Simple Beacon for ESP32 工作坊 (Windows 10)

作者：戴均民 [(taichunmin)](https://github.com/taichunmin)

* 投影片：<https://hackmd.io/@taichunmin/chatbot-tw-202002>
* 共筆：<https://hackmd.io/@chatbot-tw/chatbots-taichung-007>

## 事前準備

* Arduino IDE [(下載連結)](https://www.arduino.cc/en/main/software)
* ESP32-DevKitC 開發板 [(淘寶購買連結)](https://item.taobao.com/item.htm?id=542143157571)
* Micro USB to USB 連接線
* 在 LINE Developers console 建立 Message API 頻道 [(教學連結)](https://developers.line.biz/zh-hant/docs/messaging-api/getting-started/)

## 設定 Message API 的 Webhook

為了讓這次工作坊進行的更加順利，我寫了一個 Webhook 提供給大家在這次工作坊中使用，當然在實際使用的時候，你會需要根據自己的需求自己開發 Webhook。

::: danger
如果要使用我所提供的 Webhook，所以請記得建立一個不重要的 Message API 頻道以避免 Access Token 洩漏喔！
:::

::: tip
建立 Message API 頻道：<https://developers.line.biz/zh-hant/docs/messaging-api/getting-started/>
:::

當你建立一個 Message API 的頻道後，你需要前往 LINE Develpers console 把他設定成聊天機器人模式。

::: tip
LINE Develpers console：https://developers.line.biz/console/
:::

然後去該頻道的設定頁面，選取 Message API 分頁：

![](https://i.imgur.com/zgMcBPW.png)

在頁面中找到「Channel access token (long-lived)」的部份，並把 Access Token 複製下來。

![](https://i.imgur.com/2oFkbCW.png)

如果你的「Channel access token (long-lived)」中沒有 Access Token，你可以點選「Issue」建立一個。

::: warning
「Time until current token becomes invalid」的下拉選單保持 `0` 就可以了。
:::

![](https://i.imgur.com/L3l9Lz9.png)

在頁面中找到「Webhook settings」的部份，然後把 `https://us-central1-taichunmin.cloudfunctions.net/gcf-line-devbot/[access_token]` 填到 Webhook URL 中，並請確認底下的「Use webhook」也是啟用的狀態。

::: warning
請將上面的 `[access_token]` 替換成自己 Message API 的 Access Token！
:::

![](https://i.imgur.com/vOMMe3l.png)

最後可以按下「Verify」按鈕確認是否設定成功。

![](https://i.imgur.com/GsKaZJu.png)

若是設定成功，應該會顯示成功的訊息。

![](https://i.imgur.com/RRmlFBB.png)

接下來打開自己的 LINE，掃描 QR Code 把這個 Message API 頻道加為好友。

![](https://i.imgur.com/Ieb2GRX.png)

然後輸入「test」確認 Webhook 設定成功。

<img src="https://i.imgur.com/gNmI2kH.jpg" style="width: 480px">

> 1. 我提供的 Webhook 是放在 Google Cloud Function 上面，原始碼：<https://github.com/taichunmin/gcf-line-devbot>
> 2. 以上的 Access Token 均已在截圖後更換

## 在 Arduino IDE 中安裝 ESP32 開發板

::: warning
請先確定 Arduino IDE 已經安裝，並且先不要連接 ESP32 開發板。
:::

打開 Arduino IDE。然後從工具列點選的「檔案」🡲「偏好設定」打開偏好設定。

![](https://i.imgur.com/bWc4E0S.png)

找到偏好設定裡面的「額外的開發板管理員網址」，將 `https://dl.espressif.com/dl/package_esp32_index.json` 填寫到輸入框中。接著，按下偏好設定裡面的「確定」儲存設定。

![](https://i.imgur.com/Nu6yldY.png)

從工具列點選「工具」🡲「開發板: xxx」🡲「開發板管理員」。

![](https://i.imgur.com/MhVbkTo.png)

在開發板管理員中的搜尋框輸入 `esp32` 並且安裝「esp32」的最新版本。

![](https://i.imgur.com/mnNxhzG.png)

安裝後，應該要顯示為「INSTALLED」。

![](https://i.imgur.com/JELlSnB.png)

## 安裝 ESP32 驅動程式

為了要讓電腦能夠識別我們的 ESP32 開發板，我們需要安裝 ESP32 開發板的驅動程式。請選擇跟你作業系統相符的驅動程式下載並安裝（在本教學中是 `Windows 10`）。

::: tip
驅動程式連結：<https://www.silabs.com/products/development-tools/software/usb-to-uart-bridge-vcp-drivers>
:::

![](https://i.imgur.com/B52E4mI.png)

## 測試上傳程式到 ESP32 開發板上面

開啟 Arduino IDE，然後開啟一個新的專案。

![](https://i.imgur.com/7NnciK1.png)

將 ESP32-DevKitC 開發板使用 Micro USB to USB 連接線連接至你的電腦上。

![](https://i.imgur.com/14nWwsV.jpg)

從工具列點選「工具」🡲「開發板: xxx」🡲「ESP32 Dev Module」。

![](https://i.imgur.com/IRjmHH4.png)

從左下角系統工具列「微軟圖示」點選右鍵，然後選取「裝置管理員」。

![](https://i.imgur.com/MBJeS2r.png)

從裝置管理員中找到「連接埠 (COM 和 LPT)」🡲「Silicon Labs CP210x USB to UART Bridge」，並記下後面的 `(COM ?)`（在本教學中是 `COM5`）。

![](https://i.imgur.com/4CsG3Vk.png)

從工具列點選「工具」🡲「序列埠」🡲「COM ?」（在本教學中是 `COM5`）。

![](https://i.imgur.com/mKIPReJ.png)

確認 Arduino IDE 的右下角顯示成「ESP32 Dev Module 於 COM?」（在本教學中是 `COM5`），然後按下左上角的「上傳」按鈕測試是否設定正確。

![](https://i.imgur.com/lTt7vaW.png)

按下「上傳」按鈕後，會先進行程式碼編譯的過程，能夠在底下看到「草稿碼編譯中…」。

![](https://i.imgur.com/s8socdh.png)

編譯完成以後，就會開始上傳程式。除了狀態列會顯示上傳中，底下的黑底白字視窗中會顯示一些上傳的詳細紀錄：

![](https://i.imgur.com/3nuUACq.png)

當成功上傳以後，應該就會在底下黑底白字的區域中看到文字「Hard resetting via RTS pin...」，代表它已經幫你把 ESP32 重新開機了。

![](https://i.imgur.com/WHTEnkf.png)

## 申請一個 LINE Simple Beacon Hardware ID

LINE Simple Beacon Hardware ID，簡稱「硬體 ID」，是 LINE 為了識別這個 LINE Simple Beacon 是屬於哪個 Message API 所需要的唯一 ID，一個 Message API 最多可以申請 10 組。

::: tip
申請網址：<https://manager.line.biz/beacon/register>
:::

前往申請網址以後，你會看到的畫面如下：

![](https://i.imgur.com/AIy0CNB.png)

登入之後，請點選「發行 LINE Simple Beacon 的硬體。

![](https://i.imgur.com/lVq5c8T.png)

然後你會看到你所有的 Message API 清單，請選擇你想要發行的那個頻道。

![](https://i.imgur.com/MEbHN9K.png)

點選「發行硬體 ID」來建立一個新的硬體 ID。

> 由於我這個頻道已經發行過了，所以下面的截圖中已經有一個硬體 ID 了。

![](https://i.imgur.com/iEPgLyV.png)

這個硬體 ID 的網頁先不要關閉，接下來會用到。

## 把 LINE Simple Beacon 燒到 ESP32 內

首先，你要授權你手機上的 LINE 接收 Beacon 訊號。

::: tip
請用手機開啟這個連結：<line://nv/settings/privacy/provideUsageData/beacon>
:::

接下來你要去複製 LINE Simple Beacon for ESP32 的程式碼。

::: tip
程式碼網址：<https://github.com/taichunmin/line-simplebeacon-esp32/blob/master/sample/sample.ino>
:::

然後先把 Arduino IDE 內原本的程式碼刪除後，把新程式碼貼上。

![](https://i.imgur.com/W3ser8X.png)

接著請把第一行的「HWID」右邊的 `0000000000` 換成你上面取得的硬體 ID。

![](https://i.imgur.com/G4Svuls.png)

接下來只要按下上傳，並等候「Hard resetting via RTS pin...」的文字出現，就代表上傳成功囉！

> 如果對這個程式的除錯訊息有興趣的話，可以在上傳的時候，點選右上很像放大鏡的那個按鈕來開啟「序列埠監控視窗」，並且在右下角選擇「115200 band」，就能看到除錯訊息囉！
> 
> ![](https://i.imgur.com/nAehguh.png)

如果您的手機收到以下的訊息，恭喜你已經成功完成這個工作坊啦！

<img src="https://i.imgur.com/gD34XhQ.jpg" style="width: 480px">

## 參考資料

* [本議程投影片](https://hackmd.io/@taichunmin/chatbot-tw-202002)
* [GitHub: line/line-simple-beacon](https://github.com/line/line-simple-beacon)
* [GitHub: taichunmin/line-simplebeacon-esp32](https://github.com/taichunmin/line-simplebeacon-esp32)
* [LINE Beacon，數位導覽的小幫手！(@陳佳新)](https://www.slideshare.net/jarsing/line-beacon-220894171)
* [LINE Simple Beacon，讓你的 bot 不 Simple (@Hazel)](https://www.slideshare.net/linecorp/line-simple-beaconbotsimple)
* [Mac 也能當 LINE Beacon！](https://stringpiggy.hpd.io/mac-line-simple-beacon/)
* [Line Beacon 實作 (Python/Golang)](https://ithelp.ithome.com.tw/articles/10209703)
* [玩玩看 LINE Beacon (NodeJS/Golang)](https://www.evanlin.com/til-nodejs/)
