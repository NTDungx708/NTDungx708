@echo off
:: Mở trang web local với Chrome, cho phép load ảnh từ file://
:: Double-click file này để mở nhanh không cần lệnh

set PAGE=file:///c:/Users/Admin/Desktop/tmu/index.html
set CHROME1="%ProgramFiles%\Google\Chrome\Application\chrome.exe"
set CHROME2="%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"

if exist %CHROME1% (
    start "" %CHROME1% --allow-file-access-from-files "%PAGE%"
) else if exist %CHROME2% (
    start "" %CHROME2% --allow-file-access-from-files "%PAGE%"
) else (
    echo Chrome khong tim thay. Hay mo thu cong.
    pause
)
