@echo off
chcp 65001 >nul
echo ========================================================
echo   ONE × PIZZA — KHỞI ĐỘNG HỆ THỐNG MÁY CHỦ WEB & API
echo   Địa chỉ: 79 Hồ Tùng Mậu, Hà Nội
echo ========================================================
echo.

set PHP_BIN=php

:: Kiểm tra lệnh php trong PATH
where php >nul 2>&1
if %ERRORLEVEL% equ 0 goto found_php

:: Thử tìm các đường dẫn XAMPP / Laragon phổ biến
if exist "C:\xampp\php\php.exe" (
    set PHP_BIN=C:\xampp\php\php.exe
    goto found_php
)
if exist "C:\laragon\bin\php\php-8.2*\php.exe" (
    set PHP_BIN=C:\laragon\bin\php\php-8.2*\php.exe
    goto found_php
)

echo [CHÚ Ý] Không tìm thấy lệnh 'php' trong PATH hoặc C:\xampp\php.
echo Nếu bạn dùng XAMPP, hãy bật Apache & MySQL từ XAMPP Control Panel.
echo Hoặc cài đặt PHP và thêm thư mục chứa php.exe vào biến môi trường PATH.
echo.
pause
exit /b 1

:found_php
echo [OK] Tìm thấy PHP: %PHP_BIN%
echo.
echo ========================================================
echo   Đang khởi tạo máy chủ tại: http://localhost:8000
echo   Thiết bị khác trong mạng LAN: http://[IP-MAY-BAN]:8000
echo   Nhấn Ctrl + C để dừng máy chủ.
echo ========================================================
echo.

:: Mở trang web tự động bằng trình duyệt mặc định
start http://localhost:8000

:: Chạy PHP built-in server
"%PHP_BIN%" -S 0.0.0.0:8000
pause
