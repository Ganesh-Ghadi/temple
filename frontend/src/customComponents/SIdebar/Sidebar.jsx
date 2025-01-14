import React, { useState, useEffect } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import { IoIosArrowDown } from 'react-icons/io';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';
import {
  LayoutDashboard,
  Settings,
  Sun,
  AlignStartVertical,
  Users,
  Notebook,
  UsersRound,
  HandCoins,
  Network,
  SquareUserRound,
} from 'lucide-react';
import { ScrollArea } from '@/components/ui/scroll-area';

const Sidebar = ({ isSidebarOpen, setIsSidebarOpen }) => {
  const location = useLocation();
  const [activeParent, setActiveParent] = useState(null);

  const items = [
    {
      name: 'Dashboard',
      path: '/',
      logo: <LayoutDashboard size={16} />,
    },
    {
      name: 'Masters',
      path: '#',
      logo: <Settings size={16} />,
      children: [
        {
          name: 'Devtas',
          path: '/devtas',
          logo: <Sun size={16} />,
        },
        {
          name: 'Pooja Types',
          path: '/pooja_types',
          logo: <AlignStartVertical size={16} />,
        },
      ],
    },
    {
      name: 'User Management',
      path: '#',
      logo: <Users size={16} />,
      children: [
        {
          name: 'Roles',
          path: '/roles',
          logo: <Notebook size={16} />,
        },
        {
          name: 'Users',
          path: '/users',
          logo: <UsersRound size={16} />,
        },
      ],
    },
    {
      name: 'Denominations',
      path: '/denominations',
      logo: <HandCoins size={16} />,
    },
    {
      name: 'Services',
      path: '/services',
      logo: <Network size={16} />,
    },
    {
      name: 'Contact',
      path: '/contact',
      logo: <SquareUserRound size={16} />,
    },
  ];

  // Check if the parent item should be active
  const isParentActive = (children) => {
    return children?.some(
      (child) => location.pathname.startsWith(child.path) // Match starts with for nested routes
    );
  };

  // Update active parent on page load based on current URL
  useEffect(() => {
    const activeParent = items.find((item) => isParentActive(item.children));
    if (activeParent) {
      setActiveParent(activeParent.name);
    }
  }, [location.pathname]);

  const toggleChildren = (itemName) => {
    setActiveParent((prev) => (prev === itemName ? null : itemName));
  };

  return (
    <ScrollArea
      className={`${
        isSidebarOpen ? 'w-80' : 'w-14'
      } transition-all px-3 text-sm duration-300 min-h-screen bg-dark-purple text-white`}
    >
      <div className="flex items-center gap-x-4 mt-4 ">
        <LayoutDashboard size={32} />
        {isSidebarOpen && (
          <span className="text-2xl font-semibold">Designer</span>
        )}
      </div>
      <ul className="mt-6">
        {items.map((item, index) => {
          // const isActive =
          //   location.pathname === item.path || isParentActive(item.children);
          // const isActive =
          //   location.pathname.startsWith(item.path) ||
          //   isParentActive(item.children);
         const isActive =
           (item.path === '/' && location.pathname === '/') || // Dashboard link active only on exact `/`
           (item.path !== '/' && location.pathname.startsWith(item.path)) ||
           isParentActive(item.children);
          return (
            <li key={index}>
              <NavLink
                className={`flex my-1 items-center p-2 rounded-lg transition-all duration-300 ${
                  isActive
                    ? 'bg-dark-purple-light'
                    : 'hover:bg-dark-purple-light'
                }`}
                to={item.path || '#'}
                onClick={() => item.children && toggleChildren(item.name)}
              >
                <TooltipProvider>
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <span className="text-xl">{item.logo}</span>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>{item.name}</p>
                    </TooltipContent>
                  </Tooltip>
                </TooltipProvider>
                {isSidebarOpen && (
                  <span className="ml-4 font-medium">{item.name}</span>
                )}
                {item.children && (
                  <IoIosArrowDown
                    className={`ml-auto transition-transform ${
                      activeParent === item.name ? 'rotate-180' : ''
                    }`}
                  />
                )}
              </NavLink>
              {item.children && (
                <div
                  className={`overflow-hidden transition-all duration-300 ${
                    activeParent === item.name
                      ? 'max-h-96 opacity-100'
                      : 'max-h-0 opacity-0'
                  }`}
                >
                  {item.children.map((child, idx) => {
                    // Check if child path matches the current location
                    const isChildActive = location.pathname.startsWith(
                      child.path
                    );

                    return (
                      <NavLink
                        key={idx}
                        className={`flex my-1 items-center gap-x-4 p-2 pl-8 rounded-lg transition-all duration-300 ${
                          isChildActive
                            ? 'bg-dark-purple-light'
                            : 'hover:bg-dark-purple-light'
                        }`}
                        to={child.path}
                      >
                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <span className="text-xl">{child.logo}</span>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>{child.name}</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
                        {isSidebarOpen && (
                          <span className="font-medium">{child.name}</span>
                        )}
                      </NavLink>
                    );
                  })}
                </div>
              )}
            </li>
          );
        })}
      </ul>
    </ScrollArea>
  );
};

export default Sidebar;
//dcd
