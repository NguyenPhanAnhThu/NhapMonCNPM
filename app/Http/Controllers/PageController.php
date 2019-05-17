<?php

namespace App\Http\Controllers;
use App\Slide;
use App\Product;
use App\ProductType;
use Session;
use App\Cart;
use App\Customer;
use App\Bill;
use App\BillDetail;
use App\User;
use Hash;
use Auth;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getIndex() {
        $slide = Slide::all();
        $new_product = Product::where('new',1)->paginate(4);
        $sanpham_khuyenmai = Product::where('promotion_price','<>',0)->paginate(8);
        return view('pages.trangchu', compact('slide','new_product','sanpham_khuyenmai'));
    }
    public function getLoaiSP($type) {
        $sp_theoloai = Product::where('id_type',$type)->get();
        $sp_khac = Product::where('id_type', '<>', $type)->paginate(3);
        $loai = ProductType::all();
        $loai_sp = ProductType::where('id',$type)->first();
        return view('pages.loai_sanpham', compact('sp_theoloai', 'sp_khac', 'loai','loai_sp'));
    }
    public function getChiTiet(Request $req) {
        $sanpham = Product::where('id',$req->id)->first();
        $sanpham_tuongtu = Product::where('id_type',$sanpham->id_type)->paginate(3); 
        return view('pages.chitiet_sanpham', compact('sanpham', 'sanpham_tuongtu'));
    }
    public function getLienHe() {
        return view('pages.lienhe');
    }
    public function getGioiThieu() {
        return view('pages.gioithieu');
    }
    public function getAddToCart(Request $req, $id){
        $product = Product::find($id);
        $oldCart = Session('cart')?Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->add($product, $id);
        $req->session()->put('cart',$cart);
        return redirect()->back();
    }
    public function getDelItemCart($id) {
        $oldCart = Session::has('cart') ? Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        if(count($cart->items)>0){
            Session::put('cart', $cart);
        }
        else {
            Session::forget('cart');
        }
        return redirect()->back();
    }
    public function getCheckout(){
        if(Session::has('cart')){
            $oldCart = Session::get('cart');
            $cart = new Cart($oldCart);
            return view('pages.checkout',['cart'=>Session::get('cart'),'product_cart'=>$cart->items, 'totalPrice'=>$cart->totalPrice, 
            'totalQty'=>$cart->totalQty]);
        }
        return view('pages.checkout');
    }
    public function postCheckout(Request $req) {
        $cart = Session::get('cart');

        $customer = new Customer;
        $customer->name = $req->fullname;
        $customer->gender = $req->gender;
        $customer->email = $req->email;
        $customer->address = $req->address;
        $customer->phone_number = $req->phone;
        $customer->note = $req->note;
        $customer->save();

        $bill = new Bill;
        $bill->id_customer = $customer->id;
        $bill->id_customer = $customer->id;
        $bill->date_order = date('Y-m-d');
        $bill->total = $cart->totalPrice;
        $bill->payment =  $req->payment;
        $bill->note = $req->note;
        $bill->save();
        foreach($cart->items as $key =>$value){
            $bill_detail = new BillDetail;
            $bill_detail->id_bill = $bill->id;
            $bill_detail->id_product = $key;
            $bill_detail->quantity = $value['qty'];
            $bill_detail->unit_price = ($value['price'])/($value['qty']);
            $bill_detail->save();
        }

        Session::forget('cart');
        return redirect()->back()->with('thong-bao','Đặt hàng thành công');

    }

    public function getLogin() {
        return view('pages.login');
    }

    public function getRegister() {
        return view('pages.register');
    }
    public function postRegister(Request $req) {
        $this->validate($req,
            [
                'email' => 'required|email|unique:users,email',
                'password'=>'required|min:6|max:20',
                'fullname'=>'required',
                're_password'=> 'required|same:password',
                'phone'=>'required',
                'address'=>'required'
            ],
            [
                'email.required'=>'Vui lòng nhập Email',
                'email.email'=>'Không đúng định dạng Email',
                'email.unique'=>'Email này đã được sử dụng',
                'password.required'=>'Vui lòng nhập password',
                're_password.same'=>'Mật khẩu không giống nhau',
                'password.min'=>'Mật khẩu ít nhất 6 kí tự'              
            ]);

        $user= new User; 
        $user->full_name = $req->fullname;
        $user->email = $req->email;
        $user->address = $req->address;
        $user->password = Hash::make($req->password);
        $user->phone = $req->phone; 
        $user->save();
        return redirect()->route('login')->with('success', 'Đăng ký thành công!');

    }
    public function postLogin(Request $req) {
        $this->validate($req,
        [
            'email'=>'required|email',
            'password'=>'required|min:6|max:20',

        ],
        [
            'email.required'=>'Vui lòng nhập Email',
            'email.email'=>'Email không đúng định dạng',
            'password.required'=>'Vui lòng nhập mật khẩu',
            'password.min'=>'Mật khẩu ít nhất 6 ký tự',
        ]);
        $data = [
            'email' => $req->email,
            'password' => $req->password
        ];
        if(Auth::attempt($data)){
            return redirect()->back()->with('success','Đăng nhập thành công!');
        }
        return redirect()->back()->with('error',' Sai thông tin đăng nhập!');
    }
    function getLogout(){
        Auth::logout();
        return redirect()->route('trang-chu');
    }
    public function getSearch(Request $req) {
        $product = Product::where('name','like','%'.$req->key.'%')
                            ->orwhere('unit_price',$req->key)
                            ->get();
        return view('pages.search', compact('product'));
    }
}
