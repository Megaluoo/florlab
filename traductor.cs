using System;
using System.Reflection;
using System.Data.SqlClient;

class Program
{
    static void Main(string[] args)
    {
        try {
            string dllPath = @"c:\xampp\htdocs\Florlab\correo\PrinterServiceBase.dll";
            Assembly asm = Assembly.LoadFrom(dllPath);
            Type encryptType = asm.GetType("CLTech.Security.Encrypt");
            
            if (encryptType != null) {
                object encryptInstance = Activator.CreateInstance(encryptType);
                
                // Vemos que propiedades tiene despues de instanciar
                PropertyInfo keyProp = encryptType.GetProperty("getKey");
                PropertyInfo ivProp = encryptType.GetProperty("getIV");
                string key = keyProp != null ? (string)keyProp.GetValue(encryptInstance, null) : "";
                string iv = ivProp != null ? (string)ivProp.GetValue(encryptInstance, null) : "";
                
                Console.WriteLine("Internal Key: " + key);
                Console.WriteLine("Internal IV: " + iv);

                // Probar con el paciente de la tabla Lab21
                string encryptedName = "N+TO,QUJP"; // Lab21C2
                string encryptedOther = "DAZV>CFnNs|"; // Lab21C4
                
                MethodInfo verifityAES = encryptType.GetMethod("VerifityAES");
                if (verifityAES != null) {
                    try {
                        string name = (string)verifityAES.Invoke(encryptInstance, new object[] { encryptedName, key, iv });
                        Console.WriteLine("Descifrado 1 (AES): " + name);
                    } catch (Exception) {
                        Console.WriteLine("Fallo AES con Key/IV interno.");
                    }
                }
                
                // Tambien probar CompuAES, este devuelve void y asigna a getRDesncrypt? No, CompuAES recibe 'men'. 
                // Segun mi dump anterior: "void CompuAES(string men)", "string getRDesncrypt {get;}"
                MethodInfo compuAES = encryptType.GetMethod("CompuAES");
                PropertyInfo rDesencryptProp = encryptType.GetProperty("getRDesncrypt");
                
                if (compuAES != null && rDesencryptProp != null) {
                    try {
                        compuAES.Invoke(encryptInstance, new object[] { encryptedName });
                        string res = (string)rDesencryptProp.GetValue(encryptInstance, null);
                        Console.WriteLine("Descifrado 2 (CompuAES): " + res);
                        
                        compuAES.Invoke(encryptInstance, new object[] { encryptedOther });
                        res = (string)rDesencryptProp.GetValue(encryptInstance, null);
                        Console.WriteLine("Descifrado 3 (CompuAES): " + res);
                    } catch (Exception) {
                        Console.WriteLine("Fallo CompuAES.");
                    }
                }
                
            } else {
                Console.WriteLine("No se encontro la clase Encrypt.");
            }
        } catch (Exception ex) {
            Console.WriteLine("Error Fatal: " + ex.Message);
        }
    }
}
