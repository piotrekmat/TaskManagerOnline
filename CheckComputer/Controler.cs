using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CheckComputer
{
    class Controler
    {
        Model model = new Model();
        Form1 view;
        List<ComputerInformation> computers;
        public Controler(Form1 view)
        {
            this.view = view;
        }
        public void OnLoad()
        {

            computers=model.TakeDataAboutComputers();
            foreach(ComputerInformation computer in computers)
            {
                view.AddToCombo(computer.ComputerName);
            }
        }
        public void SetDataFromComputer(string value)
        {
            string dpa = "dddd";
            ComputerInformation computer = (from rekord in computers where rekord.ComputerName == value select rekord).FirstOrDefault();
            if (computer != null)
            {
                view.SetCpu(computer.CpuAvg);
                view.SetRam(computer.RamAvg);
                view.SetProcess(computer.ProcessAvg);
            }
        }
    }


}
