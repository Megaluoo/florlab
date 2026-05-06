using System;
using System.Data.SqlClient;

class Program
{
    static void Main()
    {
        string connStr = "Server=srvflorlab;Database=EnterpriseTest;User Id=sa;Password=@enterprise123456;";
        try {
            using (SqlConnection conn = new SqlConnection(connStr)) {
                conn.Open();
                SqlCommand cmd = new SqlCommand("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE='FUNCTION'", conn);
                using (SqlDataReader reader = cmd.ExecuteReader()) {
                    while (reader.Read()) {
                        Console.WriteLine(reader["ROUTINE_NAME"].ToString());
                    }
                }
            }
        } catch (Exception ex) {
            Console.WriteLine(ex.Message);
        }
    }
}
