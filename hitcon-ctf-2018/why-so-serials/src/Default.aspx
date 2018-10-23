<%@ Page Language="C#" %>
<script runat="server">
    protected void Button1_Click(object sender, EventArgs e) {
        if (FileUpload1.HasFile) {
            try {
                System.Web.HttpContext context = System.Web.HttpContext.Current;
                String filename = FileUpload1.FileName;
                String extension = System.IO.Path.GetExtension(filename).ToLower();
                String[] blacklists = {".aspx", ".config", ".ashx", ".asmx", ".aspq", ".axd", ".cshtm", ".cshtml", ".rem", ".soap", ".vbhtm", ".vbhtml", ".asa", ".asp", ".cer"};
                if (blacklists.Any(extension.Contains)) {
                    Label1.Text = "What do you do?";
                } else {
                    String ip = context.Request.ServerVariables["REMOTE_ADDR"];
                    String upload_base = Server.MapPath("/") + "files/" + ip + "/";
                    if (!System.IO.Directory.Exists(upload_base)) {
                        System.IO.Directory.CreateDirectory(upload_base);
                    }

                    filename = Guid.NewGuid() + extension;
                    FileUpload1.SaveAs(upload_base + filename);

                    Label1.Text = String.Format("<a href='files/{0}/{1}'>This is file</a>", ip, filename);
                }
            }
            catch (Exception ex)
            {
                Label1.Text = "ERROR: " + ex.Message.ToString();
            }
        } else {
            Label1.Text = "You have not specified a file.";
        }
    }
</script>

<!DOCTYPE html>
<html>
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
    <title>Why so Serials?</title>
</head>
<body>
  <div class="container">
    <div class="jumbotron" style='background: #f7f7f7'>
        <h1>Why so Serials?</h1>
        <p>May the <b><a href='Default.aspx.txt'>source</a></b> be with you!</p>
        <br />
        <form id="form1" runat="server">
            <div class="input-group">
                <asp:FileUpload ID="FileUpload1" runat="server" class="form-control"/>
                <span class="input-group-btn">
                    <asp:Button ID="Button1" runat="server" OnClick="Button1_Click" 
                 Text="GO" class="btn"/>
                </span>
            </div>
            <br />
            <br />
            <br />
            <div class="alert alert-primary text-center">
                <asp:Label ID="Label1" runat="server"></asp:Label>
            </div>
        </form>
    </div>
  </div>
</body>
</html>
