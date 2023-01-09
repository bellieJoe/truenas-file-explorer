const jsftp = require("jsftp");

const ftp = new jsftp({
    host: "192.168.1.121",
    port: 21, 
    user: "bellie", 
    pass: "12345678"
  });

//   ftp.connect(function(err) {
//     if (err) throw err;
  
//     console.log("Connected to FTP server");
  
//     // Upload a file
//     ftp.put("C:\Users\Bellie Joe\Desktop\Bellie Joe Jandusay December 16 - 31.xlsx", "/sample", function(err) {
//       if (err) throw err;
  
//       console.log("File uploaded successfully");
  
//       // Close the FTP connection
//       ftp.raw.quit(function(err, data) {
//         if (err) throw err;
  
//         console.log("Disconnected from FTP server");
//       });
//     });
//   });