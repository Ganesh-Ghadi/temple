import React, { useEffect, useRef, useState } from "react";
import Sidebar from "../customComponents/SIdebar/Sidebar";
import { Outlet, useNavigate } from "react-router-dom";
import MobileSidebar from "../customComponents/SIdebar/MobileSidebar";
import { FaRegMoon } from "react-icons/fa";
import { LuSunMedium } from "react-icons/lu";
import logo from "../assets/react.svg";
import { TbLogout2 } from "react-icons/tb";
import { toast } from "react-toastify";
import axios from "axios";
import Navbar from "../customComponents/Navbar/Navbar";
import { ScrollArea, ScrollBar } from "@/components/ui/scroll-area";

const MainLayout = ({ toggleTheme, darkMode }) => {
  return (
    <>
      {/* <div className="w-full">
        <Navbar />
        <div className="w-full p-7">
          <Outlet />
        </div>
      </div> */}
      {/* <div className="w-full flex  flex-col">
        <Navbar />
        <div className="w-full flex overflow-hidden">
          <div className="h-screen">
            <Sidebar />
          </div>
          <div className="w-full p-7 flex flex-col overflow-y-auto">
            <Outlet />
          </div>
        </div>
      </div> */}

      <div className="flex flex-col h-screen">
        <Navbar />

        <div className="flex flex-1 overflow-hidden">
          {/* <div className="min-h-screen auo"> */}

          <Sidebar />
          {/* </div> */}

          <div className="w-full bg-slate-50 overflow-auto p-1 dark:bg-[#070B1D]  ">
            <Outlet />
            {/* <ScrollBar orientation="horizontal" below replace div with scrollArea /> */}
          </div>
        </div>
      </div>
    </>
  );
};

export default MainLayout;
