using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using WebAPI.Models;
using System.Security.Cryptography;
using System.Text;
using System.IO;

namespace WebAPI.Helpers
{
    public class Authorize
    {
        private DB db = new DB();
        private static byte[] initVectorBytes = Encoding.ASCII.GetBytes("E5F4C132EF6DDLC");
        private static int keysize = 256;

        //Checks if user is user
        public Boolean UserKey(string apiKey)
        {
            try
            {
                db.ApiKeys.Where(o => o.Key == apiKey).Single();
                return true;
            }
            catch
            {
                return false;
            }
        }

        //Checks if user is Admin
        public Boolean AdminKey(string apiKey)
        {
            try
            {
                int UserId = db.ApiKeys.Where(o => o.Key == apiKey).Single().UserId;

                if (db.Users.Where(o => o.Id == UserId).Single().Admin > 0)
                    return true;
                else
                    return false;
            }
            catch
            {
                return false;
            }
        }

        public String Encrypt(string plainText, string passPhrase)
        {
            byte[] plainTextBytes = Encoding.UTF8.GetBytes(plainText);

            using (PasswordDeriveBytes password = new PasswordDeriveBytes(passPhrase, null))
            {
                byte[] keyBytes = password.GetBytes(keysize / 8);
                using (RijndaelManaged symmetricKey = new RijndaelManaged())
                {
                    symmetricKey.Mode = CipherMode.CBC;
                    using (ICryptoTransform encryptor = symmetricKey.CreateEncryptor(keyBytes, initVectorBytes))
                    {
                        using (MemoryStream memoryStream = new MemoryStream())
                        {
                            using (CryptoStream cryptoStream = new CryptoStream(memoryStream, encryptor, CryptoStreamMode.Write))
                            {
                                cryptoStream.Write(plainTextBytes, 0, plainTextBytes.Length);
                                cryptoStream.FlushFinalBlock();
                                byte[] cipherTextBytes = memoryStream.ToArray();
                                return Convert.ToBase64String(cipherTextBytes);
                            }
                        }
                    }
                }
            }
        }


        public string Decrypt(string cipherText, string passPhrase)
        {
            byte[] cipherTextBytes = Convert.FromBase64String(cipherText);

            using (PasswordDeriveBytes password = new PasswordDeriveBytes(passPhrase, null))
            {
                byte[] keyBytes = password.GetBytes(keysize / 8);
                using (RijndaelManaged symmetricKey = new RijndaelManaged())
                {
                    symmetricKey.Mode = CipherMode.CBC;
                    using (ICryptoTransform decryptor = symmetricKey.CreateDecryptor(keyBytes, initVectorBytes))
                    {
                        using (MemoryStream memoryStream = new MemoryStream(cipherTextBytes))
                        {
                            using (CryptoStream cryptoStream = new CryptoStream(memoryStream, decryptor, CryptoStreamMode.Read))
                            {
                                byte[] plainTextBytes = new byte[cipherTextBytes.Length];
                                int decryptedByteCount = cryptoStream.Read(plainTextBytes, 0, plainTextBytes.Length);
                                return Encoding.UTF8.GetString(plainTextBytes, 0, decryptedByteCount);
                            }
                        }
                    }
                }
            }
        }
    }
}