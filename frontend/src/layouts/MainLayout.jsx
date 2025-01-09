import React, { useEffect, useRef, useState } from 'react';
import Sidebar from '../customComponents/SIdebar/Sidebar';
import { Outlet, useNavigate } from 'react-router-dom';
import MobileSidebar from '../customComponents/SIdebar/MobileSidebar';
import { FaRegMoon } from 'react-icons/fa';
import { LuSunMedium } from 'react-icons/lu';
import logo from '../assets/react.svg';
import { TbLogout2 } from 'react-icons/tb';
import axios from 'axios';
import Navbar from '../customComponents/Navbar/Navbar';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import Logout from '../customComponents/Navbar/Logout';
import { toast } from 'sonner';

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuPortal,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { AlignJustify, LogOut } from 'lucide-react';
import { ModeToggle } from '@/components/ModeToggle';

import { Button } from '@/components/ui/button';

const MainLayout = ({ toggleTheme, darkMode }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [isLogoutDialogOpen, setIsLogoutDialogOpen] = useState(false);
  const user = JSON.parse(localStorage.getItem('user'));
  const token = user.token;
  const navigate = useNavigate();
  const logout = async () => {
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/logout', {
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      console.log(response);
      toast.success('Logged-out successfully');
      localStorage.removeItem('user');
      navigate('/login');
    } catch (error) {
      console.log(error);
      if (error.response) {
        toast.error('logout failed: ' + error.response.data); // Customize error message
      } else if (error.request) {
        toast.error('No response from server. Please try again later.');
      } else {
        toast.error('An error occurred while logout.');
      }
    }
  };

  const handleLogout = async () => {
    setIsLogoutDialogOpen(false); // Close the dialog first
    setIsOpen(false); // Close the dropdown
    await logout(); // Perform logout
  };
  return (
    <>
      <div className="flex flex-col h-screen">
        <div className="flex flex-1 overflow-hidden">
          <Sidebar />
          <div className="w-full bg-slate-50 overflow-auto p-1 dark:bg-[#070B1D]  ">
            <div className=" w-full px-5 py-3 flex justify-end items-center">
              <div className="px-5">
                <ModeToggle></ModeToggle>
              </div>
              <DropdownMenu open={isOpen} onOpenChange={setIsOpen}>
                <DropdownMenuTrigger asChild>
                  <Button
                    variant="outline"
                    size="icon"
                    className="overflow-hidden rounded-full"
                  >
                    <img
                      src={logo}
                      width={36}
                      height={36}
                      alt="Avatar"
                      className="overflow-hidden rounded-full"
                    />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel>My Account</DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem>Settings</DropdownMenuItem>
                  <DropdownMenuItem>Support</DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={() => setIsLogoutDialogOpen(true)}>
                    <LogOut /> Logout
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
            <Outlet />
          </div>
        </div>
        {isLogoutDialogOpen && (
          <Logout
            onLogout={handleLogout}
            closeDialog={() => setIsLogoutDialogOpen(false)}
          />
        )}
      </div>
    </>
  );
};

export default MainLayout;
