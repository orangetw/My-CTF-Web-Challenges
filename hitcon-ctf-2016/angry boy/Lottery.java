import java.io.*;
import java.util.*;
import java.security.*;
import javax.servlet.*;
import javax.servlet.http.*;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;


import static java.nio.file.Files.readAllBytes;
import static java.nio.file.Paths.get;

public class Lottery extends HttpServlet {

  public static String SECRET_PATH = "/SECRET";
  public static String FLAG_PATH   = "/FLAG";
  public static String IV = "0011223344556677";

  public byte[] get_key() throws IOException {
    return Arrays.copyOfRange(readAllBytes(get(SECRET_PATH)), 0, 16);
  }

  public byte[] get_flag() throws IOException {
    return readAllBytes(get(FLAG_PATH));
  }

  public byte[] md5(String s) throws NoSuchAlgorithmException, UnsupportedEncodingException {
    MessageDigest md = MessageDigest.getInstance("MD5");
    byte[] digest = md.digest(s.getBytes("UTF-8"));
    return digest;
  }

  public void print_msg(HttpServletResponse response, String s) throws IOException {
    PrintWriter out = response.getWriter();
    s = s.replace("\\", "\\\\");
    s = s.replace("\"", "\\\"");
    out.println("{\"msg\": \"" + s + "\"}");
  }

  public boolean is_win(byte c, int line) throws IOException {
    if (c == get_key()[line]) {
      return true;
    } else {
      return false;
    }
  }

  public int string_to_int(String s) throws NumberFormatException {
    return Integer.parseInt(s);
  }

  public String bytes_to_hex(byte[] b) {
    StringBuilder s = new StringBuilder();
    for (int i=0; i<b.length; i++){
      s.append( String.format("%02x", b[i]) );
    }

    return s.toString();
  }

  public String get_random_byte(int len) {
    char[] base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".toCharArray();
    SecureRandom random = new SecureRandom();
    StringBuilder s = new StringBuilder();
    for (int i=0; i<len; i++){
      int index = random.nextInt(0xffff) % base.length;
      s.append( base[index] );
    }

    return s.toString();
  }

  public void doGet(HttpServletRequest request, 
                    HttpServletResponse response) throws ServletException, IOException {

    response.setContentType("text/html");
    response.setCharacterEncoding("UTF-8");
    HttpSession session = request.getSession();
    PrintWriter out = response.getWriter();

    // set session
    session.setAttribute("prefix", get_random_byte(16));
    

    RequestDispatcher rd = request.getRequestDispatcher("WEB-INF/jsp/index.jsp");
    rd.forward(request, response);

  }

  public void doPost(HttpServletRequest request, 
                    HttpServletResponse response) throws ServletException, IOException {
    // init
    response.setContentType("application/json");
    response.setCharacterEncoding("UTF-8");
    HttpSession session = request.getSession();
    PrintWriter out = response.getWriter();

    // get prefix
    String prefix = "";
    Object p = session.getAttribute("prefix");
    if (p == null) {
      prefix = get_random_byte(16);
    } else {
      prefix = (String)p;
    }

    // renew 
    session.setAttribute("prefix", get_random_byte(16));

    // calculate captcha
    byte[] digest = {0};
    String captcha = request.getParameter("captcha");
    try {
      digest = md5(prefix + captcha);
    } catch (NoSuchAlgorithmException e) {

    }

    // check type of line is number
    int line = string_to_int(request.getParameter("line"));
    String good = "";
    if (line >= 0 && line <= 3) {
      good = "333";
    } else if (line >= 4 && line <= 7) {
      good = "4444";
    } else if (line >= 8 && line <= 11) {
      good = "55555";
    } else if (line >= 12 && line <= 15) {
      good = "666666";
    } else {
      print_msg(response, "line error");
      return ;
    }


    // check guess
    byte guess;
    p = request.getParameter("guess");
    if (p == null || ((String)p).equals("") ) {
      print_msg(response, "guess not found");
      return ;
    } else {
      guess = (byte)request.getParameter("guess").toCharArray()[0];
    }
    
    if ( bytes_to_hex(digest).startsWith(good) ) {
      if (is_win(guess, line)) {

        if (line == 15){

          byte[] skey = {0};
          try {
            skey = md5(request.getRemoteAddr() + (new String(get_key())));
          } catch (NoSuchAlgorithmException e) {
            
          }

          try {
            SecretKeySpec skeySpec = new SecretKeySpec(skey, "AES");
            Cipher cipher = Cipher.getInstance("AES/CBC/PKCS5Padding");
            IvParameterSpec iv = new IvParameterSpec(IV.getBytes("UTF-8"));
            cipher.init(Cipher.ENCRYPT_MODE, skeySpec, iv);
            byte[] encrypted = cipher.doFinal(get_flag());

            print_msg(response, "encrypted flag is " + bytes_to_hex(encrypted));
          } catch (Exception e){
            print_msg(response, "encryption error");  
          }

        } else {
          print_msg(response, "good");
        }
          
      } else {
        print_msg(response, "bad luck");
      }
    } else {
      print_msg(response, "captcha error");
    }
  }

  public void destroy() {
    // pass
  }
}
