PROMPT TRIỂN KHAI DỰ ÁN WEBSITE ONE X PIZZA
Bạn là IDE agent phụ trách thiết lập và triển khai một dự án website thực tế cho người dùng cuối, đồng thời hỗ trợ nhóm sinh viên trình bày đồ án môn Thiết kế và triển khai website. Hãy đọc toàn bộ brief trước khi làm. Chủ động tạo dự án, chạy thử, kiểm tra các luồng quan trọng và bàn giao mã nguồn, cách chạy cùng danh sách chức năng thực sự hoàn thành. Không chỉ tạo ảnh mockup hoặc mô tả kế hoạch.
1. Bối cảnh và ranh giới đã thống nhất
- Tên thương hiệu: One x Pizza; có thể hiển thị wordmark ONE × PIZZA trên giao diện. Đây là nhà hàng pizza giả định do nhóm tự sáng tạo, không liên quan sở hữu với Pizza 4P’s hoặc các nhà hàng tham khảo.
- Đề tài: Website quảng bá nhà hàng One x Pizza. Người dùng cuối là khách muốn tìm hiểu nhà hàng, xem thực đơn, địa điểm và gửi yêu cầu đặt bàn. Người quản trị cập nhật thực đơn/nội dung và xử lý yêu cầu đặt bàn.
- Điểm cốt lõi: trang chủ chỉ giới thiệu tổng quan các phần và dẫn đường. Nội dung, dữ liệu và thao tác chuyên sâu phải nằm ở các trang riêng để các thành viên trong nhóm có phần việc độc lập. Không nhồi toàn bộ thực đơn, chi tiết địa điểm hoặc form đặt bàn đầy đủ lên trang chủ.
- Ba yêu cầu nghiệp vụ phải hoạt động trong sản phẩm cuối: thông tin địa điểm, thực đơn phục vụ, chức năng đặt bàn. Bản UI hiện có mới là mẫu thiết kế, không được coi là các chức năng đã cài đặt.
- Phần quản trị không đặt trong điều hướng dành cho khách; đi qua đường dẫn quản trị riêng và có đăng nhập.
- Nhóm có 9 người: một nhóm trưởng, bảy thành viên làm dự án, một thư ký chỉ chỉnh sửa bản Word sau. Cấu trúc mã nên có module/trang rõ ràng để giao việc và tích hợp.
2. Tham chiếu và tài nguyên hiện có
Các trang chỉ dùng để nghiên cứu cách tổ chức trải nghiệm, không sao chép logo, ảnh, nội dung hoặc giao diện nguyên bản:
- Buffet Sen Á Đông: https://buffetsenadong.vn/
- Pizza 4P’s Tràng Tiền: https://pizza4ps.com/vn/location/922/
- Giang Mỹ: https://banhtranggiangmy.com/
- Pizza Belga: https://pizzabelga.com/
- The Pizza Company: https://thepizzacompany.vn/
- Pizza Home: https://pizzahome.vn/
- Pizza Hut: https://pizzahut.vn/
- Pizza 4P’s: https://pizza4ps.com/vn
Bản UX/UI đã dựng trong workspace để kế thừa hướng bố cục, tone màu và cấu trúc, nhưng có thể refactor thành mã dễ bảo trì:
- Trang chủ: /workspace/scratch/e3eee55a7e63/one-x-pizza/One-x-Pizza-Trang-chu-UXUI-v1.html
- Trang Giới thiệu sơ bộ: /workspace/scratch/e3eee55a7e63/one-x-pizza/gioi-thieu.html
- Ảnh hero gốc do dự án tạo: /workspace/scratch/e3eee55a7e63/one-x-pizza/hero-pizza.png
Trang chủ hiện đã được sửa thành tổng quan chức năng, không còn form đặt bàn ở trang chủ. Trang Giới thiệu hiện có bản đồ và thông tin nhà hàng ở dạng chỗ điền, chưa có địa chỉ thật. Các mẫu tĩnh này là cơ sở UX/UI, không phải mã nền bắt buộc hay chức năng đã hoàn thiện.
3. Định hướng thương hiệu và giao diện
- Vibe: pizza thủ công, hiện đại, ấm cúng, chú trọng trải nghiệm gặp gỡ và chia sẻ. Chất lượng thị giác gọn, có cảm giác nhà hàng thực sự, không giống template giao đồ ăn đại trà. Ảnh món ăn là tâm điểm, khoảng thở rộng, chữ dễ đọc, nút hành động nổi bật.
- Thông điệp đề xuất: “Mỗi lát pizza, một cuộc hẹn.”; câu phụ “Pizza ngon hơn khi có nhau.” Nội dung có thể được nhóm hiệu chỉnh nhưng cần thống nhất trên toàn site. Tránh bịa tuyên bố kiểm chứng được như “số 1”, giải thưởng, chứng nhận, năm thành lập, nguyên liệu độc quyền.
- Bảng màu gốc tham khảo bản UI: kem #F6F0E6, nền sáng #FFFAF2, đỏ gạch #A54030, olive #596747, than đậm #1D211F, chữ #241D19. Dùng nhất quán; đảm bảo tương phản chữ/nền và trạng thái hover/focus.
- Typography gợi ý: display serif tương tự Playfair Display cho tiêu đề; sans tương tự DM Sans cho nội dung/UI; có font fallback. Có thể dùng font local/system nếu môi trường không truy cập Google Fonts.
- Responsive: mobile, tablet, desktop. Header desktop có logo, Trang chủ, Giới thiệu, Thực đơn, Đặt bàn, Liên hệ và CTA Đặt bàn; mobile có menu mở/đóng truy cập bằng bàn phím. Chân trang có liên kết chính và thông tin nhà hàng khi dữ liệu được chốt.
- Ảnh hero đang có là tài nguyên concept của dự án. Các hình đĩa pizza vẽ bằng CSS trong bản mẫu chỉ là hình minh họa; ở bản hoàn chỉnh thay bằng ảnh sản phẩm phù hợp, có quyền sử dụng, hoặc để placeholder có chủ đích. Không tái sử dụng ảnh/logo từ các website tham khảo khi chưa được phép.
4. Kiến trúc thông tin và yêu cầu từng trang
4.1. Trang chủ /
Trang tổng quan và định tuyến. Các khối: hero với định vị thương hiệu và CTA; giới thiệu ngắn có liên kết đến Giới thiệu; teaser các nhóm thực đơn có liên kết đến Thực đơn; teaser trải nghiệm/không gian; teaser quy trình đặt bàn có liên kết đến trang Đặt bàn; thông tin liên hệ ngắn/footer. Không có toàn bộ danh mục sản phẩm, form đặt bàn chi tiết hay bản đồ đầy đủ ở đây. CTA phải mở đúng trang/section có nội dung tương ứng, không được là liên kết giả # hoặc tự trỏ lại chính teaser.
4.2. Giới thiệu /gioi-thieu
Trang riêng khi khách nhấn Giới thiệu ở header. Có ba phần chính: About/câu chuyện One x Pizza, Google Maps/địa điểm, thông tin nhà hàng gồm địa chỉ, giờ mở cửa, điện thoại và email/kênh liên hệ. Có thể bổ sung ảnh không gian. Chưa có địa chỉ, số điện thoại, giờ mở cửa chính thức do nhóm chốt: lưu các trường này ở cấu hình/seed có nhãn CẦN NHÓM CHỐT trong môi trường phát triển, hoặc dùng dữ liệu demo được công khai là giả định; không bịa thông tin thành sự thật. Chỉ nhúng Google Maps theo đúng tọa độ hoặc địa chỉ nhóm phê duyệt. Trước đó hiển thị khung bản đồ thông báo vị trí đang hoàn thiện, không ghim một địa điểm sai.
4.3. Thực đơn /thuc-don và chi tiết món /thuc-don/:slug
Phân nhóm tối thiểu: pizza, món ăn kèm/mì, đồ uống. Trang danh sách có ảnh, tên, mô tả ngắn, giá bằng VND, danh mục; có lọc nhóm và tìm kiếm nếu dễ triển khai, trạng thái rỗng rõ ràng. Trang chi tiết món hiển thị ảnh, tên, thành phần/mô tả, giá và liên kết quay lại. Món mẫu được tự biên soạn riêng cho One x Pizza, dữ liệu nhất quán giữa UI và cơ sở dữ liệu. Không lấy nguyên tên, ảnh và mô tả món của đối thủ.
4.4. Đặt bàn /dat-ban
Trang chức năng đầy đủ, độc lập với teaser ở trang chủ. Biểu mẫu tối thiểu: họ tên, số điện thoại, ngày, giờ, số khách, ghi chú tùy chọn. Kiểm tra bắt buộc, định dạng liên hệ, số khách > 0, ngày giờ không ở quá khứ; xử lý lỗi và thông báo thành công rõ ràng. Khi gửi hợp lệ, ghi một yêu cầu vào cơ sở dữ liệu, tạo mã tham chiếu và trạng thái ban đầu pending; không nói “đã xác nhận bàn” khi quản trị viên chưa xác nhận. Cần trang hoặc thông báo kết quả để khách biết yêu cầu đã được tiếp nhận. Không hứa kiểm tra bàn trống theo thời gian thực nếu hệ thống chưa có dữ liệu sức chứa/lịch bàn. Phải chống gửi trùng do bấm liên tiếp ở mức hợp lý.
4.5. Liên hệ /lien-he
Hiển thị thông tin liên lạc và đường dẫn tới trang địa điểm/đặt bàn. Nếu có form phản hồi thì phải lưu được hoặc gửi theo cơ chế thực sự hoạt động; nếu chưa làm, giữ trang thông tin liên hệ thay vì form giả.
4.6. Quản trị /admin
Đăng nhập với mật khẩu được lưu an toàn; không hardcode mật khẩu trên frontend. Bảng điều khiển tối thiểu có danh sách yêu cầu đặt bàn, chi tiết và đổi trạng thái pending/confirmed/cancelled; quản lý món/danh mục ở mức CRUD nếu thời gian cho phép. Phân biệt phần bắt buộc (xem và xử lý đặt bàn) với phần mở rộng (CRUD thực đơn, bài viết/ưu đãi). Có thể tạo dữ liệu seed cho demo. Không công khai số điện thoại khách ở giao diện công khai.
4.7. Tin tức/ưu đãi và các phần mở rộng
Tin tức/ưu đãi có thể là teaser hoặc trang riêng nếu nhóm có năng lực và dữ liệu. Giỏ hàng, thanh toán, giao hàng, tài khoản khách hàng, tích điểm là định hướng phát triển, không phải chức năng bắt buộc của đề tài website quảng bá. Không tự triển khai chúng trước khi ba yêu cầu cốt lõi vận hành tốt.
5. Dữ liệu và luồng nghiệp vụ
Đề xuất mô hình tối thiểu:
- restaurant_info: tên, giới thiệu, địa chỉ, tọa độ hoặc URL map, giờ mở cửa, điện thoại, email, ảnh không gian.
- categories: id, name, slug, sort_order.
- menu_items: id, category_id, name, slug, description, ingredients, price_vnd, image, available, sort_order.
- reservations: id, reference_code, customer_name, phone, booking_date, booking_time, party_size, note, status, created_at, updated_at.
- admins: id, username/email, password_hash, created_at.
- posts chỉ khi có chức năng tin tức/ưu đãi thực sự.
Quan hệ: một danh mục có nhiều món; mỗi món thuộc một danh mục. Đặt bàn là yêu cầu của khách, độc lập với món ăn. Nếu tạo bảng tables hoặc khung giờ thì phải có nghiệp vụ quản lý sức chứa tương ứng; không thêm chỉ để sơ đồ đẹp.
Luồng demo chính: khách vào trang chủ → mở Thực đơn và xem món → mở Giới thiệu để xem địa điểm/thông tin → mở Đặt bàn và gửi yêu cầu → dữ liệu xuất hiện trong quản trị → quản trị đổi trạng thái. Có dữ liệu thử và cách reset dữ liệu để trình diễn lại.
6. Công nghệ và cấu trúc dự án
Trước khi code, kiểm tra repo/môi trường hiện có và yêu cầu của giảng viên nếu có. Chưa chốt ngôn ngữ/công nghệ; đừng coi mục lục mẫu liệt kê HTML, CSS, JavaScript, PHP và MySQL là ràng buộc. Nếu không có hệ thống sẵn, ưu tiên giải pháp dễ học, dễ chạy cục bộ và dễ thuyết trình: frontend HTML/CSS/JavaScript, backend Node.js/Express và SQLite; có thể dùng công cụ build nhẹ nếu cần. Nếu môn học buộc dùng PHP/MySQL, chuyển backend sang PHP/MySQL và giữ các hành vi, luồng, dữ liệu như brief. Ghi rõ quyết định cuối cùng trong README và tài liệu Chương 4; chỉ kể công nghệ thực sự được dùng.
Tách mã theo phần để nhóm chia việc: giao diện chung/header/footer/tokens; trang chủ; Giới thiệu/địa điểm; Thực đơn; Đặt bàn; API/dữ liệu; quản trị. Dùng component/layout chung, URL rõ ràng, cấu hình môi trường mẫu, script khởi tạo và dữ liệu demo. Không phụ thuộc vào đường dẫn tạm của bản mockup sau khi đã đưa asset cần thiết vào dự án. Trang lỗi/404 và trạng thái tải/lỗi cơ bản nên có.
7. Tiêu chí hoàn thành và kiểm thử
- Có lệnh cài và chạy dự án từ máy mới; README ghi phiên bản runtime, biến môi trường, cách tạo DB/seed, tài khoản demo quản trị (chỉ trong tài liệu nội bộ demo), cách chạy kiểm thử.
- Truy cập được mọi trang từ header/footer; không có nút và liên kết giả. Desktop và mobile không tràn ngang, ảnh không bị méo, chữ dễ đọc, biểu mẫu dùng được bằng bàn phím, có label và trạng thái focus.
- Địa điểm, thực đơn và đặt bàn hiển thị/hoạt động như mô tả; dữ liệu đặt bàn thực sự tồn tại sau tải lại trang, xuất hiện trong quản trị. Kiểm tra đầu vào sai, ngày quá khứ, gửi hợp lệ, đổi trạng thái.
- Không đưa dữ liệu cá nhân thật vào seed hoặc ảnh chụp demo. Giá/địa chỉ demo nếu chưa chốt phải được đánh dấu là giả định trong dữ liệu chuẩn bị, không trình bày là sự thật thương mại.
- Bàn giao danh sách rõ: đã cài đặt, chưa cài đặt, ảnh màn hình các trang, sơ đồ chức năng và mô hình dữ liệu. Không ghi “hoàn thành” những tính năng chỉ mới có giao diện.
8. Gắn dự án vào báo cáo thảo luận
Báo cáo Word sẽ do thư ký chỉnh sau; hãy giúp nhóm cung cấp chứng cứ cho đúng năm yêu cầu:
1. Lý do xây dựng, đơn vị sở hữu, người dùng: One x Pizza là đơn vị sở hữu giả định; khách hàng và quản trị viên là hai nhóm người dùng.
2. Yêu cầu nội dung, chức năng, giao diện: ghi đúng chức năng nhóm định làm.
3. Thiết kế nội dung, chức năng, giao diện, mô hình dữ liệu: cung cấp sitemap/sơ đồ phân cấp, wireframe/ảnh UI, sơ đồ dữ liệu.
4. Ngôn ngữ/công nghệ và demo: ghi theo mã đã chạy; cung cấp kịch bản demo từng chức năng.
5. Đánh giá và kết luận: nêu kết quả kiểm thử, giới hạn thực tế và hướng phát triển.
Mục lục mẫu có Lời mở đầu; Chương 1 Tổng quan; Chương 2 Các yêu cầu; Chương 3 Thiết kế; Chương 4 Công nghệ và triển khai/demo; Chương 5 Đánh giá; Kết luận. Chương 4 phải tùy chỉnh theo công nghệ thật và có phần demo, không sao chép tên các ngôn ngữ trong mẫu khi không dùng.
9. Cách làm việc với người dùng và bàn giao theo giai đoạn
1. Đầu tiên kiểm tra bản mẫu hiện có, tóm tắt cấu trúc dự án sẽ tạo, những dữ liệu kinh doanh còn thiếu và lựa chọn công nghệ. Giữ các thông tin chưa được nhóm chốt ở một file cấu hình/nội dung mẫu dễ thay, không để chúng rải rác trong mã.
2. Dựng foundation: layout chung, route, design tokens, responsive; giữ trang chủ là trang tổng quan. Cho người dùng xem và duyệt hướng giao diện trước khi viết dàn trải các trang chuyên sâu.
3. Triển khai theo module: Giới thiệu/địa điểm, Thực đơn, Đặt bàn, quản trị; tích hợp dữ liệu chung. Mỗi module có kết quả có thể xem và kiểm tra độc lập để giao việc cho thành viên.
4. Chạy thử toàn bộ luồng, sửa lỗi rồi bàn giao mã, tài liệu chạy, danh sách phần việc có thể phân công cho bảy thành viên và tư liệu cho thư ký làm Word.
Nếu thiếu địa chỉ, giờ mở cửa, số điện thoại, menu/giá hoặc ràng buộc ngôn ngữ của giảng viên, hãy nêu giả định tạm cho bản demo và đánh dấu rõ phần chờ duyệt. Vẫn hoàn thành phần việc độc lập với các quyết định đó. Khi người dùng cung cấp thông tin chính thức, cập nhật tập trung rồi chạy lại kiểm thử liên quan.