/**
 * js/data.js — One x Pizza
 * Seed data, restaurant config, categories, menu items
 */

const RESTAURANT_CONFIG = {
  name: "One x Pizza",
  wordmark: "ONE × PIZZA",
  tagline: "Mỗi lát pizza, một cuộc hẹn.",
  subTagline: "Pizza ngon hơn khi có nhau.",
  story: `One x Pizza ra đời từ niềm tin rằng một bữa ăn ngon nhất không phải ở nguyên liệu đắt tiền, mà ở những người ngồi cùng bàn. Chúng tôi làm pizza thủ công, từng chiếc bánh, từng lần, với đam mê thật sự.`,
  storyExtra: `Mỗi công thức là câu chuyện riêng — kết hợp giữa kỹ thuật Ý truyền thống và nguyên liệu địa phương tươi ngon nhất Việt Nam. Đến với One x Pizza là đến với không gian ấm cúng, nơi thời gian chậm lại và mỗi lát pizza là một cuộc hẹn đáng nhớ.`,

  address: "79 Hồ Tùng Mậu, Hà Nội",
  addressShort: "79 Hồ Tùng Mậu, Hà Nội",
  phone: "0869564200",
  email: "79hotungmau@gmail.com",
  // mapUrl: backward-compat alias for mapDirectionsUrl
  mapUrl: "https://www.google.com/maps/search/79+H%E1%BB%93+T%C3%B9ng+M%E1%BA%ADu,+H%C3%A0+N%E1%BB%99i",
  mapDirectionsUrl: "https://www.google.com/maps/dir/?api=1&destination=79+H%E1%BB%93+T%C3%B9ng+M%E1%BA%ADu+H%C3%A0+N%E1%BB%99i",
  mapEmbedUrl: "https://maps.google.com/maps?q=79+H%E1%BB%93+T%C3%B9ng+M%E1%BA%ADu+H%C3%A0+N%E1%BB%99i&output=embed&z=16",
  hours: [
    { days: "Thứ Hai – Thứ Sáu", time: "10:00 – 22:00" },
    { days: "Thứ Bảy – Chủ Nhật", time: "09:00 – 22:30" },
  ],
  hoursNote: "Nhà bếp đóng trước 30 phút khi hết giờ phục vụ.",
  social: {
    facebook: "#",
    instagram: "#",
    zalo: "#",
  }
};


const CATEGORIES = [
  { id: 1, name: "Pizza", slug: "pizza", icon: "🍕", color: "#A54030" },
  { id: 2, name: "Món kèm", slug: "mon-kem", icon: "🥗", color: "#596747" },
  { id: 3, name: "Đồ uống", slug: "do-uong", icon: "🥤", color: "#4A6B8A" },
];

const MENU_ITEMS = [
  // ── Pizza ────────────────────────────────────────────
  {
    id: 1, categoryId: 1,
    name: "Pizza Ký Ức",
    slug: "pizza-ky-uc",
    description: "Phiên bản Margherita được thổi hồn bằng sốt cà chua tự nấu và phô mai mozzarella tươi. Đơn giản mà đáng nhớ.",
    ingredients: "Bột bánh tươi ủ 48 giờ, sốt cà chua tự nấu, phô mai mozzarella tươi, lá basil, dầu olive extra virgin, muối biển",
    price: 189000,
    image: "images/menu/pizza-ky-uc.jpg",
    emoji: "🍕",
    available: true,
    featured: true,
    tag: "Bán chạy"
  },
  {
    id: 2, categoryId: 1,
    name: "Pizza Hoàng Hôn",
    slug: "pizza-hoang-hon",
    description: "Sự kết hợp táo bạo giữa sốt BBQ khói nhẹ, thịt bò Wagyu và ớt jalapeño — phù hợp với những ai thích vị đậm đà.",
    ingredients: "Bột bánh tươi, sốt BBQ khói, thịt bò Wagyu thái mỏng, phô mai cheddar, hành tây caramel, jalapeño, mùi tây",
    price: 249000,
    image: "images/menu/pizza-hoang-hon.jpg",
    emoji: "🥩",
    available: true,
    featured: true,
    tag: "Đặc biệt"
  },
  {
    id: 3, categoryId: 1,
    name: "Pizza Vườn Xanh",
    slug: "pizza-vuon-xanh",
    description: "Thuần chay với rau củ tươi theo mùa, sốt pesto và phô mai ricotta béo ngậy. Nhẹ nhàng mà trọn vị.",
    ingredients: "Bột bánh tươi, sốt pesto húng quế, ricotta, cà chua bi, ớt chuông, nấm portobello, bí xanh, hạt thông",
    price: 199000,
    image: "images/menu/pizza-vuon-xanh.jpg",
    emoji: "🥦",
    available: true,
    featured: true,
    tag: "Chay"
  },
  {
    id: 4, categoryId: 1,
    name: "Pizza Biển Cả",
    slug: "pizza-bien-ca",
    description: "Tôm tươi, mực thái lát và sốt kem trắng — mang hương vị biển vào bàn ăn, tươi sạch từ nguyên liệu chọn lọc.",
    ingredients: "Bột bánh tươi, sốt béchamel, tôm sú, mực ống, hành lá, phô mai parmesan, tỏi phi, chanh vàng",
    price: 229000,
    image: "images/menu/pizza-bien-ca.jpg",
    emoji: "🦐",
    available: true,
    featured: true,
    tag: "Hải sản"
  },
  {
    id: 5, categoryId: 1,
    name: "Pizza Bình Minh",
    slug: "pizza-binh-minh",
    description: "Trứng ốp la, xúc xích Ý, pancetta và phô mai bốn loại — chiếc pizza buổi sáng để cả ngày dài thêm ý nghĩa.",
    ingredients: "Bột bánh tươi, sốt cà chua, xúc xích Ý, pancetta, trứng gà tươi, phô mai mozzarella, gouda, parmesan, pecorino",
    price: 219000,
    image: "images/menu/pizza-binh-minh.jpg",
    emoji: "🍳",
    available: true,
    featured: true,
    tag: "Mới"
  },
  // ── Món kèm ───────────────────────────────────────────
  {
    id: 6, categoryId: 2,
    name: "Salad Caprese",
    slug: "salad-caprese",
    description: "Cà chua heirloom, phô mai mozzarella tươi, basil và dầu olive — màu sắc tươi sáng, vị thanh sảng khoái.",
    ingredients: "Cà chua heirloom đa màu, mozzarella tươi, lá basil, dầu olive, muối biển, tiêu đen, balsamic reduction",
    price: 89000,
    image: "images/menu/salad-caprese.jpg",
    emoji: "🥗",
    available: true,
  },
  {
    id: 7, categoryId: 2,
    name: "Bruschetta Bốn Vị",
    slug: "bruschetta-bon-vi",
    description: "Bánh mì baguette nướng giòn phết bốn topping: cà chua-basil, avocado-muối biển, tapenade olive, và ricotta-mật ong.",
    ingredients: "Baguette tươi, cà chua, basil, avocado, olive đen, ricotta, mật ong hoa, muối biển, dầu olive",
    price: 79000,
    image: "images/menu/bruschetta.jpg",
    emoji: "🥖",
    available: true,
  },
  {
    id: 8, categoryId: 2,
    name: "Soup Cà Chua Rau Củ",
    slug: "soup-ca-chua",
    description: "Súp cà chua truyền thống nấu theo kiểu Toscana với rau củ mùa và gia vị thảo mộc. Ấm bụng và đủ dinh dưỡng.",
    ingredients: "Cà chua San Marzano, cần tây, cà rốt, hành tây, tỏi, ngò tây, húng tây, dầu olive, muối-tiêu",
    price: 69000,
    image: "images/menu/soup-ca-chua.jpg",
    emoji: "🍲",
    available: true,
  },
  // ── Đồ uống ───────────────────────────────────────────
  {
    id: 9, categoryId: 3,
    name: "Nước Chanh Leo",
    slug: "nuoc-chanh-leo",
    description: "Chanh tươi pha với nước ép chanh dây và một chút siro gừng — chua ngọt, mát lạnh, giải nhiệt hoàn hảo.",
    ingredients: "Chanh tươi, chanh dây, siro gừng, đường mía, đá tươi",
    price: 49000,
    image: "images/menu/nuoc-chanh-leo.jpg",
    emoji: "🍋",
    available: true,
  },
  {
    id: 10, categoryId: 3,
    name: "Latte Matcha Mật Ong",
    slug: "latte-matcha",
    description: "Matcha premium Nhật hòa cùng sữa tươi nguyên kem và mật ong hoa — điểm cân bằng hoàn hảo giữa đắng và ngọt.",
    ingredients: "Bột matcha Nhật Bản grade A, sữa tươi nguyên kem, mật ong hoa, đường",
    price: 59000,
    image: "images/menu/latte-matcha.jpg",
    emoji: "🍵",
    available: true,
  },
  {
    id: 11, categoryId: 3,
    name: "Soda Dâu Tây Mint",
    slug: "soda-dau-tay",
    description: "Dâu tây tươi xay nhuyễn, lá bạc hà và soda — màu hồng rực rỡ, vị nhẹ nhàng và thơm mát, thích hợp kết hợp với pizza.",
    ingredients: "Dâu tây tươi, lá bạc hà, soda, siro đơn, chanh tươi, đá",
    price: 45000,
    image: "images/menu/soda-dau-tay.jpg",
    emoji: "🍓",
    available: true,
  },
];

// ── Seed Reservations (demo) ─────────────────────────────
const SEED_RESERVATIONS = [
  {
    id: "r-seed-001",
    referenceCode: "OXP-20260928-001",
    customerName: "Nguyễn Văn Minh",
    phone: "0901 234 567",
    bookingDate: "2026-10-05",
    bookingTime: "19:00",
    partySize: 4,
    note: "Bàn gần cửa sổ nếu có",
    status: "confirmed",
    createdAt: "2026-09-28T10:30:00"
  },
  {
    id: "r-seed-002",
    referenceCode: "OXP-20260928-002",
    customerName: "Trần Thị Lan",
    phone: "0912 876 543",
    bookingDate: "2026-10-06",
    bookingTime: "18:30",
    partySize: 2,
    note: "Kỷ niệm ngày cưới, cần hỗ trợ đặt hoa",
    status: "pending",
    createdAt: "2026-09-28T14:15:00"
  },
  {
    id: "r-seed-003",
    referenceCode: "OXP-20260929-001",
    customerName: "Lê Hoàng Phúc",
    phone: "0933 456 789",
    bookingDate: "2026-10-10",
    bookingTime: "20:00",
    partySize: 8,
    note: "",
    status: "pending",
    createdAt: "2026-09-29T09:00:00"
  },
  {
    id: "r-seed-004",
    referenceCode: "OXP-20260927-001",
    customerName: "Phạm Thị Thu",
    phone: "0888 111 222",
    bookingDate: "2026-09-27",
    bookingTime: "12:00",
    partySize: 3,
    note: "",
    status: "cancelled",
    createdAt: "2026-09-25T16:45:00"
  }
];

// ── API Configuration ─────────────────────────────────────
// Hệ thống Backend PHP & MySQL chính thức
const API_CONFIG = {
  baseUrl: (function () {
    if (window.location.protocol === 'file:') {
      return 'http://localhost:8000/backend/api';
    }
    const path = window.location.pathname;
    let base = path;
    if (base.includes('/admin/')) {
      base = base.substring(0, base.indexOf('/admin/'));
    } else if (base.endsWith('/admin')) {
      base = base.substring(0, base.indexOf('/admin'));
    } else {
      base = base.substring(0, base.lastIndexOf('/'));
    }
    return (base === '' ? '' : base) + '/backend/api';
  })()
};

